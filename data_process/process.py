from datetime import date, datetime
import pandas
import numpy
import matplotlib.pyplot as mpl
import mysql.connector
import nltk

def estimador_amamentacao_AMEX(dhInicio, dhFim, idade, dh) -> float:
    x = dh - date.fromisoformat(idade)
    z = datetime.fromisoformat(dhFim) - datetime.fromisoformat(dhInicio)
    return 591 - 0.7*x.days + 0.76*z.seconds*60

def estimador_amamentacao_AMEC(kcalorias, idade, dh) -> float:
    x = dh - date.fromisoformat(idade)
    return 755-0.48*x.days-0.59*kcalorias

def estimador_amamentacao_nMamadas(nMamadas, idade, dh) -> float:
    x = dh - date.fromisoformat(idade)
    return 489-0.63*x.days+13.45*nMamadas

def agrupamento_mamadas_proximas():
    pass

def processar_mamadeira():
    pass

def processar_alimentcao_solida():
    pass

nltk.download('punkt')
conx = mysql.connector.connect(host='127.0.0.1', port='3306', user=user, password=pw, database=db)
cursor = conx.cursor(buffered=True)
query = ("""SELECT *
            FROM `aimov`.`seioMaterno`
            WHERE crianca = %(crianca)s AND dhInicio BETWEEN %(dataInicio)s AND %(dataFim)s AND dhFim IS NOT NULL
        """)
cursor.execute(query)
seio = cursor.fetchall()
columnSeio = cursor.column_names
nSeio = cursor.rowcount

query = ("""SELECT *
            FROM `aimov`.`mamadeira`
            WHERE crianca = %(crianca)s AND dh BETWEEN %(dataInicio)s AND %(dataFim)s
        """)
cursor.execute(query)
mamadeira = cursor.fetchall()
columnMamadeira = cursor.column_names
nMamadeira = cursor.rowcount

query = ("""SELECT *
            FROM `aimov`.`refeicaoSolida`
            WHERE crianca = %(crianca)s AND dh BETWEEN %(dataInicio)s AND %(dataFim)s
        """)
cursor.execute(query)
solido = cursor.fetchall()
columnSolido = cursor.column_names
nSolido = cursor.rowcount

dfP = pandas.DataFrame(seio, columns=columnSeio)
dfM = pandas.DataFrame(mamadeira, columns=columnMamadeira)
dfS = pandas.DataFrame(solido, columns=columnSolido)
resLeite = pandas.DataFrame()

if nSeio > 0:
    amex = True
    if nSolido > 0:
        amex = False
        pass
    elif nMamadeira > 0:
        resLeite = dfM[dfM['alimento'] == 'LEITEM']
        if len(resLeite) != nMamadeira:
            amex = False
    if amex:
        estima_volume_amex = dfM.apply('estimador_amamentacao_AMEX', 1, False, 'reduce')
        #calcular a média de mamadas por dia, para então...
        agrupa_ame = estima_volume_amex.groupby('data').count()
        media_diaria_esperada_ame = round(agrupa_ame['dh'].apply(numpy.average))

        #interpolar os resultados presentes para deduzir os pontos de mamada ausentes. https://pandas.pydata.org/docs/reference/api/pandas.DataFrame.interpolate.html
        #listar as deduções em tabela
    else:
        #AMEC estimator?
        pass
    leiteMaterno_mesclado = pandas.concat([resLeite, dfP], ignore_index=True)
    #agrupar leiteMaterno_mesclado por data
    horarios_leitem = leiteMaterno_mesclado['dhInicio']
    leiteMaterno_mesclado['dh'].fillna(horarios_leitem, inplace=True)
    leiteMaterno_mesclado['data'] = leiteMaterno_mesclado['dh'].dt.date
    leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['dh'].dt.time

    #para cada DF do agrupamento, montar uma list contendo a quantidade de mamadas encontradas
    qtd_mamadas = leiteMaterno_mesclado.groupby('data').count()

    resLeite['data'] = resLeite['dh'].dt.date
    volume_mamadeira_somado = resLeite.groupby('data')['volume'].apply(numpy.sum)
    estimativa_diaria = []
    for index, valor in qtd_mamadas['dhInicio'].iteritems():
        estimativa_diaria.append(estimador_amamentacao_nMamadas(valor, idadec, index))

    estimativa_diaria = pandas.Series(estimativa_diaria, qtd_mamadas.index)

    if amex and nMamadeira > 0:
        resultados_estimadores = pandas.DataFrame([estima_volume_amex, estima_volume_nMamadas])
        resultados_estimadores['data'] = resultados_estimadores['dh'].dt.date
        resultados_estimadores['hora'] = resultados_estimadores['dh'].dt.time
        resultados_estimadores.drop('dh',1)
        resultados_estimadores.groupby('data').agg({'volume': numpy.average})

        mpl.figure()
        resultados_estimadores.plot(x='data', y='volume', kind='bar', use_index=False)
        mpl.savefig('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/testA.png')
    else:
        if amex==False:
            mpl.figure()
            estimativa_diaria.plot(kind='bar')
            mpl.savefig('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/testA.png')
        else:
            pass
    media_por_mamada_seio = ((estimativa_diaria-volume_mamadeira_somado) / qtd_mamadas['dh']).repeat(qtd_mamadas['dhInicio'].array) #comparar em gráfico de barras empilhadas os dois tipos de leite, invés da barra somatório
    media_por_mamada_seio.rename('idx', inplace=True)
    media_por_mamada_seio.index = leiteMaterno_mesclado[leiteMaterno_mesclado['volume'].isna()].index
    #plotar: volumes (barras) e scatter (horarios); as mamadeiras em legenda especial no caso do scatter
    leiteMaterno_mesclado['volume'].fillna(media_por_mamada_seio, inplace=True)
    leiteMaterno_mesclado['hora'] = pandas.Series(leiteMaterno_mesclado['dh'].dt.time, dtype='string') #codificar como número?
    leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['hora'].str.replace(':','', regex=False)
    leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['hora'].str.slice(stop=4)
    leiteMaterno_mesclado['hora'] = pandas.Series(leiteMaterno_mesclado['hora'], dtype='int')

    mpl.figure()
    leiteMaterno_mesclado.plot.scatter(x='hora', y='volume', use_index=False)
    mpl.savefig('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/testB.png')

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
        ax.bar(dados['data'], dados['volume'], width=0.5, bottom=y_offset, label=tipo) #TODO: Criar switch
        y_offset = y_offset + dados['volume']

    ax.legend()
    ax.set_ylabel('Volume, ml')
    ax.set_xlabel('Data')
    mpl.savefig('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/testC.png')
    mpl.xticks(numpy.arange(0,len(diferentes_datas)), ticks.dt.date, rotation=90)
    mpl.draw()
    #imprimir as tabelas

if nSolido > 0:
    tesauro = pandas.read_csv('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/tesauro-alimentos.csv', names=['alimento', 'categoria'], skiprows=1)
    tesauro['alimento'] = tesauro['alimento'].apply(str.lower)
    tesauro['alimento'] = tesauro['alimento'].apply(str.strip)
    texto_demo = open('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/amostra.txt', 'r')
    texto_demo2 = open('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/amostra2.txt', 'r')
    conteudo_texto_demo = texto_demo.read()
    conteudo_texto_demo = conteudo_texto_demo + ' ' + texto_demo2.read()

    fdist = nltk.FreqDist(word.lower() for word in nltk.word_tokenize(conteudo_texto_demo))
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
    soma_categoria.to_json('/home/igor/Documentos/UNESP/O TCC - Ivan the Terrible/export.json', 'index')