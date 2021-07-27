from os import getcwd
import re
import pandas
import numpy
import matplotlib.pyplot as mpl
import mysql.connector
import nltk
import argparse

def estimador_amamentacao_AMEX(dhInicio, dhFim, idade) -> float:
    x = dhInicio.date() - idade
    z = dhFim - dhInicio
    return 591 - 0.7*x.days + 0.76*z.seconds*60

def estimador_amamentacao_AMEC(kcalorias, idade, dh) -> float:
    x = dh - idade
    return 755-0.48*x.days-0.59*kcalorias

def estimador_amamentacao_nMamadas(nMamadas, idade, dh) -> float:
    x = dh - idade
    return 489-0.63*x.days+13.45*nMamadas

def switch_tipo_liquido(tipo : str) -> str:
    switcher = {
        'LEITEF' : 'Fórmula infantil',
        'LEITEM' : 'Leite materno',
        'LEITED' : 'Outros leites',
        'AGUA' : 'Água',
        'CHA' : 'Chás',
        'SUCO' : 'Sucos'
    }
    return switcher.get(tipo)

def nome_arquivo_exportacao(tipo : str, extensao : str, args : dict) -> str:
    return '{prefix}/data_process/export/{id}-{di}-{df}-{tipo}.{extensao}'.format(prefix=getcwd(), id=args.get('crianca'), di=args.get('dataInicio'), df=args.get('dataFim'), tipo=tipo, extensao=extensao)

def obter_dados_mysql() -> dict:
    try:
        conf = open('{}/.env.local.php'.format(getcwd()))
    except FileNotFoundError:
        try:
            conf = open('{}/.env'.format(getcwd()))
        except FileNotFoundError:
            return {}
    continuar = True
    while continuar:
        l = conf.readline()
        if l == '':
            conf.close()
            return {}
        _ = re.search(r'DATABASE_URL', l)
        if _ != None:
            res = re.search(r'//(?P<user>\w+):(?P<password>\w+)@(?P<host>\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(?P<port>\d{2,5})/(?P<database>\w+)',l)
            continuar = False
    conf.close()
    return res.groupdict()

parser = argparse.ArgumentParser()
parser.add_argument("crianca", type=int)
parser.add_argument("dInicio", type=str)
parser.add_argument("dFim", type=str)
args = parser.parse_args()

i_arg = {'crianca': args.crianca,
    "dataInicio": args.dInicio,
    "dataFim": args.dFim
    }

bd = obter_dados_mysql()
conx = mysql.connector.connect(host=bd.get('host'), port=bd.get('port'), user=bd.get('user'), password=bd.get('password'), database=bd.get('database'))
cursor = conx.cursor(buffered=True)
query = ("""SELECT *
            FROM `seio_materno`
            WHERE crianca_id = %(crianca)s AND dh_inicio BETWEEN %(dataInicio)s AND %(dataFim)s AND dh_fim IS NOT NULL
        """)
cursor.execute(query, i_arg)
seio = cursor.fetchall()
columnSeio = cursor.column_names
nSeio = cursor.rowcount

query = ("""SELECT *
            FROM `mamadeira`
            WHERE crianca_id = %(crianca)s AND dh BETWEEN %(dataInicio)s AND %(dataFim)s
        """)
cursor.execute(query, i_arg)
mamadeira = cursor.fetchall()
columnMamadeira = cursor.column_names
nMamadeira = cursor.rowcount

query = ("""SELECT *
            FROM `refeicao_solida`
            WHERE crianca_id = %(crianca)s AND dh BETWEEN %(dataInicio)s AND %(dataFim)s
        """)
cursor.execute(query, i_arg)
solido = cursor.fetchall()
columnSolido = cursor.column_names
nSolido = cursor.rowcount

query = ("""SELECT *
            FROM `crianca`
            WHERE id = %(crianca)s
        """)
cursor.execute(query, {'crianca': args.crianca})
crianca = cursor.fetchall()
idadec = crianca[0][2]

dfP = pandas.DataFrame(seio, columns=columnSeio)
dfM = pandas.DataFrame(mamadeira, columns=columnMamadeira)
dfS = pandas.DataFrame(solido, columns=columnSolido)
dfP['dh_inicio'] = pandas.to_datetime(dfP['dh_inicio'], format='%d-%m-%Y %H:%M:%S')
dfP['dh_fim'] = pandas.to_datetime(dfP['dh_fim'], format='%d-%m-%Y %H:%M:%S')
resLeite = dfM[dfM['alimento'] == 'LEITEM']

if nSeio > 0:
    amex = True

    if nSolido > 0:
        amex = False
    elif nMamadeira > 0:
        if len(resLeite) != nMamadeira:
            amex = False

    if amex:
        #PREPARAÇÃO
        estimativa_diaria = dfP.copy()
        estimativa_diaria.loc[:,'volume'] = 0
        estimativa_diaria.loc[:, 'duracao'] = 0
        estimativa_diaria['dInicio'] = estimativa_diaria['dh_inicio'].dt.date
        estimativa_diaria['hInicio'] = estimativa_diaria['dh_inicio'].dt.time
        estimativa_diaria['dFim'] = estimativa_diaria['dh_fim'].dt.date
        estimativa_diaria['hFim'] = estimativa_diaria['dh_fim'].dt.time

        #ESTIMATIVA ATRAVÉS DE REGRESSORES
        agrupa_ame = estimativa_diaria.groupby('dInicio')
        agrupa_ame_conta = agrupa_ame.count()

        volumesX = []
        volumesN = []
        if resLeite.empty:
            for index, valores in estimativa_diaria.iterrows():
                volumesX.append(estimador_amamentacao_AMEX(valores['dh_inicio'], valores['dh_fim'], idadec))
                volumesN.append(estimador_amamentacao_nMamadas(agrupa_ame_conta.loc[valores['dInicio'], 'dh_inicio'], idadec, valores['dInicio']))

            volumes = numpy.array([volumesX, volumesN])
            volumes = numpy.mean(volumes, 0)
            estimativa_diaria['volume'] = pandas.Series(volumes)

            #vamos colcoar os volumes estimados para cada sessão
            agrupa_ame_tempo = estimativa_diaria.apply(lambda x: x['dh_fim'] - x['dh_inicio'], axis=1)
            agrupa_ame_tempo = agrupa_ame_tempo.apply(lambda x: x.seconds / 60)
            estimativa_diaria['duracao'] = agrupa_ame_tempo

            agrupa_ame = estimativa_diaria.groupby('dInicio')
            agrupa_ame_soma_volume = agrupa_ame['volume'].agg('mean')
            agrupa_ame_soma_tempo = agrupa_ame['duracao'].agg('sum')

            estimativa_diaria['volume'] = estimativa_diaria.apply(lambda x: agrupa_ame_soma_volume.loc[x['dInicio']] / agrupa_ame_soma_tempo.loc[x['dInicio']] * agrupa_ame_tempo[int(x.name)], axis=1)

        else:
            leiteMaterno_mesclado = pandas.concat([resLeite, dfP], ignore_index=True)

            #se houver registros de LM em mamadeira, organziar as colunas de data e hora, além de obter a quantidade de leite ingerido por mamadeira
            horarios_leitem = leiteMaterno_mesclado['dh_inicio']
            volume_mamadeira_somado = pandas.Series()
            leiteMaterno_mesclado['dh'].fillna(horarios_leitem, inplace=True)
            leiteMaterno_mesclado['dh'] = pandas.to_datetime(leiteMaterno_mesclado['dh'])
            leiteMaterno_mesclado['dInicio'] = leiteMaterno_mesclado['dh'].dt.date
            leiteMaterno_mesclado['hInicio'] = leiteMaterno_mesclado['dh'].dt.time
            resLeite['data'] = resLeite['dh'].dt.date
            volume_mamadeira_somado = resLeite.groupby('data')['volume'].apply(numpy.sum)

            #para cada DF do agrupamento, montar uma list contendo a quantidade de mamadas encontradas
            qtd_mamadas = leiteMaterno_mesclado.groupby('dInicio').count()

            #estimando o volume de cada mamada no seio materno
            resultados_estimador = []
            for index, valor in qtd_mamadas['dh'].iteritems():
                resultados_estimador.append(estimador_amamentacao_nMamadas(valor, idadec, index))

            #preparo e inserção dos valores de mamada de leite
            _ = pandas.Series(resultados_estimador, qtd_mamadas.index)
            _ = _.repeat(qtd_mamadas['dhFim'].to_list())
            _ = _ / qtd_mamadas['dhFim'].repeat(qtd_mamadas['dhFim'].to_list())
            _ = _.reset_index()
            _.index = leiteMaterno_mesclado[leiteMaterno_mesclado['volume'].isna()].index
            _.rename(columns={0: 'volume'}, inplace=True)

            leiteMaterno_mesclado['volume'].update(_['volume'])
            estimativa_diaria = leiteMaterno_mesclado.copy()

            # calculo de marcações temporais
            agrupa_ame_tempo = estimativa_diaria.apply(lambda x: x['dhFim'] - x['dhInicio'], axis=1)
            agrupa_ame_tempo = agrupa_ame_tempo.apply(lambda x: x.seconds / 60)
            estimativa_diaria['duracao'] = agrupa_ame_tempo
            estimativa_diaria['dFim'] = estimativa_diaria['dhFim'].dt.date
            estimativa_diaria['hFim'] = estimativa_diaria['dhFim'].dt.time
            estimativa_diaria.drop('dhInicio', axis=1, inplace=True)
            estimativa_diaria.rename({'dh':'dhInicio'}, axis=1, inplace=True)

        #agrupamento de mamadas
        passo = 1
        drops = []
        to_update = pandas.DataFrame()
        for i in range(len(estimativa_diaria)-1):
            row = estimativa_diaria.iloc[i]
            if row['lado'] == numpy.NaN:
                continue

            proximo = estimativa_diaria.iloc[i+passo]
            while (passo > 0):
                if (proximo['dhInicio'] - row['dhFim']).seconds < 300:
                    row['volume'] = row['volume'] + proximo['volume']
                    row['dhFim'] = proximo['dhFim']
                    row['dFim'] = proximo['dFim']
                    row['hFim'] = proximo['hFim']
                    row['duracao'] = row['duracao'] + proximo['duracao']
                    drops.append(i+passo)
                    passo = passo + 1
                    try:
                        proximo = estimativa_diaria.iloc[i+passo]
                    except:
                        i = i + passo
                        passo = 0
                else:
                    i = i + passo
                    passo = 0
            passo = 1
            to_update = to_update.append(row)

        #
        estimativa_diaria.update(to_update)
        estimativa_diaria.drop(index=drops, inplace=True)
        # #estimativa_diaria.sort_values(by='hInicio', inplace=True)

        # #calcular a média de mamadas por dia, para então...
        agrupa_ame = estimativa_diaria.groupby('dInicio')
        # agrupa_ame_conta = agrupa_ame.count()
        # agrupa_ame_soma = agrupa_ame.agg({'dhInicio': 'count', 'volume': 'mean'})
        # agrupa_ame_soma = agrupa_ame_soma['volume'] / agrupa_ame_soma['dhInicio']

        # media_diaria_esperada_ame = round(agrupa_ame_conta['dhInicio'].agg('average'))
        # #interpolar os resultados presentes para deduzir os pontos de mamada ausentes. https://pandas.pydata.org/docs/reference/api/pandas.DataFrame.interpolate.html
        # dIntervalo = date.fromisoformat(dataFim) - date.fromisoformat(dataInicio)
        # hIntervalo = 24 / media_diaria_esperada_ame
        # matriz_entradas = numpy.zeros((media_diaria_esperada_ame, dIntervalo.days))
        # #quais os dias estão fora da média, para baixo?

        # #listar as deduções em tabela

        #gráfico de barras -- consumo diário de LM diário estimado
        agrupa_ame_soma = agrupa_ame.agg({'volume': 'sum'})
        mpl.figure()
        agrupa_ame_soma.plot(y='volume', kind='bar')
        mpl.savefig(nome_arquivo_exportacao('leiteMaterno', 'png', i_arg))
        mpl.draw()

        estimativa_diaria['hora'] = pandas.Series(estimativa_diaria['dhInicio'].dt.time, dtype='string')
        estimativa_diaria['hora'] = estimativa_diaria['hora'].str.replace(':','', regex=False)
        estimativa_diaria['hora'] = estimativa_diaria['hora'].str.slice(stop=4)
        estimativa_diaria['hora'] = pandas.Series(estimativa_diaria['hora'], dtype='int')
        mpl.figure()
        estimativa_diaria.plot.scatter(x='hora', y='volume', use_index=False)
        mpl.savefig(nome_arquivo_exportacao('scatterLeiteMaterno', 'png', i_arg))
        mpl.draw()
    else:
        leiteMaterno_mesclado = pandas.concat([resLeite, dfP], ignore_index=True)
        #se houver registros de LM em mamadeira, organziar as colunas de data e hora, além de obter a quantidade de leite ingerido por mamadeira
        if resLeite.empty == False:
            horarios_leitem = leiteMaterno_mesclado['dh_inicio']
            volume_mamadeira_somado = pandas.Series()
            leiteMaterno_mesclado['dh'].fillna(horarios_leitem, inplace=True)
            leiteMaterno_mesclado['data'] = leiteMaterno_mesclado['dh'].dt.date
            leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['dh'].dt.time
            resLeite['data'] = resLeite['dh'].dt.date
            volume_mamadeira_somado = resLeite.groupby('data')['volume'].apply(numpy.sum)
        else:
            leiteMaterno_mesclado['data'] = leiteMaterno_mesclado['dh_inicio'].dt.date
            leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['dh_inicio'].dt.time

        #para cada DF do agrupamento, montar uma list contendo a quantidade de mamadas encontradas
        qtd_mamadas = leiteMaterno_mesclado.groupby('data').count()

        #estimando o volume de cada mamada no seio materno
        estimativa_diaria = []
        for index, valor in qtd_mamadas['dh_inicio'].iteritems():
            estimativa_diaria.append(estimador_amamentacao_nMamadas(valor, idadec, index))
            #AMEC estimator?

        estimativa_diaria = pandas.Series(estimativa_diaria, qtd_mamadas.index)

        if amex and nMamadeira > 0:
            #se, apesar de utilizar mamadeira, somente leite materno é oferecido
            estimativa_diaria['data'] = estimativa_diaria['dh'].dt.date
            estimativa_diaria['hora'] = estimativa_diaria['dh'].dt.time
            estimativa_diaria.drop('dh',1)
            estimativa_diaria.groupby('data').agg({'volume': numpy.average})

            mpl.figure()
            estimativa_diaria.plot(x='data', y='volume', kind='bar', use_index=False)
            mpl.savefig(nome_arquivo_exportacao('leiteMaterno', 'png', i_arg))
            mpl.draw()
        else:
            if amex==False:
                mpl.figure()
                estimativa_diaria.plot(kind='bar')
                mpl.savefig(nome_arquivo_exportacao('leiteMaterno', 'png', i_arg))
                mpl.draw()

        media_por_mamada_seio = ((estimativa_diaria-volume_mamadeira_somado) / qtd_mamadas['dh']).repeat(qtd_mamadas['dh_inicio'].array) #comparar em gráfico de barras empilhadas os dois tipos de leite, invés da barra somatório
        media_por_mamada_seio.rename('idx', inplace=True)
        media_por_mamada_seio.index = leiteMaterno_mesclado[leiteMaterno_mesclado['volume'].isna()].index
        #plotar: volumes (barras) e scatter (horarios); as mamadeiras em legenda especial no caso do scatter
        leiteMaterno_mesclado['volume'].fillna(media_por_mamada_seio, inplace=True)
        leiteMaterno_mesclado['hora'] = pandas.Series(leiteMaterno_mesclado['dh'].dt.time, dtype='string')
        leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['hora'].str.replace(':','', regex=False)
        leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['hora'].str.slice(stop=4)
        leiteMaterno_mesclado['hora'] = pandas.Series(leiteMaterno_mesclado['hora'], dtype='int')

        mpl.figure()
        leiteMaterno_mesclado.plot.scatter(x='hora', y='volume', use_index=False)
        mpl.savefig(nome_arquivo_exportacao('scatterLeiteMaterno', 'png', i_arg))
        mpl.draw()

if nMamadeira > 0:
    #retirar resultados contendo leite materno
    if resLeite.empty != False:
        mamadeira = dfM[dfM['alimento'] != 'LEITEM']
    else:
        mamadeira = dfM
    #seaprar os tipos de alimentos disponíveis em df ou grupos (o que por sua vez já permite pegar os dataframes separados)
    #criar tabelas-somatório com as médias diárias de consumo de cada um dos tipos de alimentos encontrados
    mamadeira['data'] = pandas.Series(dfM['dh'].dt.date)
    mamadeira.drop('dh', 1)
    resultados_mamadeira = mamadeira.groupby(['data','alimento']).agg({'volume': numpy.sum})

    idx = resultados_mamadeira.index.to_frame(index=False)
    idx.drop('alimento', 1, inplace=True)
    idx.drop_duplicates(inplace=True)
    ticks = pandas.Series(idx['data'], dtype='datetime64[ns]')
    idx['data'] = ticks
    idx['dia'] = idx['data'].dt.day
    idx['mes'] = idx['data'].dt.month
    idx['ano'] = idx['data'].dt.year
    idx['ano'] = idx['ano'].apply(lambda x: x* (3600*24*30*12))
    idx['mes'] = idx['mes'].apply(lambda x: x* (3600*24*30))
    idx['dia'] = idx['dia'].apply(lambda x: x* (3600*24))
    idx.drop('data',1, inplace=True)
    novo_index_data = idx.apply(numpy.sum,1, result_type='reduce').to_list()
    resultados_mamadeira.index = resultados_mamadeira.index.set_levels(novo_index_data, 0)
    resultados_mamadeira.reset_index(inplace=True)

    diferentes_alimentos = resultados_mamadeira['alimento'].unique()
    diferentes_datas = resultados_mamadeira['data'].unique()

    y_offset = numpy.zeros(len(resultados_mamadeira['data'].unique()))
    fig, ax = mpl.subplots()

    for tipo in diferentes_alimentos:
        dados = resultados_mamadeira[resultados_mamadeira['alimento'] == tipo]

        if len(dados['data']) != len(diferentes_datas):
            ausentes = diferentes_datas[numpy.in1d(diferentes_datas, dados['data'].array, invert=True)]
            arr_dados_ausentes = numpy.zeros((len(ausentes),3))
            for i in range(len(ausentes)):
                arr_dados_ausentes[i][0] = ausentes[i]
                arr_dados_ausentes[i][1] = 0
                arr_dados_ausentes[i][2] = 0
            dados = dados.append(pandas.DataFrame(arr_dados_ausentes, columns=dados.columns), ignore_index=True)
            dados = dados.sort_values(by=['data'])
        dados['data'] = pandas.Series([x for x in range(len(diferentes_datas))])
        ax.bar(dados['data'], dados['volume'], width=0.5, bottom=y_offset, label=switch_tipo_liquido(tipo))
        y_offset = y_offset + dados['volume']

    ax.legend()
    ax.set_ylabel('Volume, ml')
    ax.set_xlabel('Data')
    mpl.xticks(numpy.arange(0,len(diferentes_datas)), ticks.dt.date, rotation=90)
    mpl.savefig(nome_arquivo_exportacao('somaMamadeira', 'png', i_arg))
    mpl.draw()
    #imprimir as tabelas

if nSolido > 0:

    nltk.download('punkt')

    tesauro = pandas.read_csv('{}/data_process/tesauro-alimentos.csv'.format(getcwd()), names=['alimento', 'categoria'], skiprows=1)
    tesauro['alimento'] = tesauro['alimento'].apply(str.lower)
    tesauro['alimento'] = tesauro['alimento'].apply(str.strip)

    conteudo_texto = ''
    for entrada in dfS['anotacao'].iteritems():
        conteudo_texto = conteudo_texto + ' ' + str(entrada)

    fdist = nltk.FreqDist(word.lower() for word in nltk.word_tokenize(conteudo_texto))
    tokens = list(fdist.keys()) #[x for x in fdist]
    freqs = []
    for el in tokens:
        freqs.append(fdist[el])

    dfDistr = pandas.DataFrame(data={'token':pandas.Series(tokens),'frequencia':pandas.Series(freqs)})
    alimentos_encontrados = dfDistr[dfDistr['token'].isin(tesauro['alimento'])]
    alimentos_encontrados.sort_values(by='token', inplace=True)
    alimentos_encontrados.reset_index(drop=True, inplace=True)
    _ = tesauro[tesauro['alimento'].isin(alimentos_encontrados['token'])]
    _.sort_values(by='alimento', inplace=True)
    _.reset_index(drop=True, inplace=True)
    alimentos_encontrados['categoria'] = _['categoria'] #opcional detalhado
    soma_categoria = alimentos_encontrados.groupby('categoria').agg(numpy.sum)
    soma_categoria.to_json(nome_arquivo_exportacao('tabelaSolidos', 'json', i_arg), 'index')