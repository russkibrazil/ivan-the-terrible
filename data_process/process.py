from datetime import date, datetime
import pandas
import numpy
import mysql.connector


def estimador_amamentacao_AMEX(idade, inicio, fim) -> float:
    x = date.today() - date.fromisoformat(idade)
    z = datetime.fromisoformat(fim) - datetime.fromisoformat(inicio)
    return 591 - 0.7*x.days + 0.76*z.seconds*60

def estimador_amamentacao_AMEC(idade, kcalorias) -> float:
    x = date.today() - date.fromisoformat(idade)
    return 755-0.48*x.days-0.59*kcalorias

def estimador_amamentacao_nMamadas(idade, nMamadas) -> float:
    x = date.today() - date.fromisoformat(idade)
    return 489-0.63*x.days+13.45*nMamadas

def agrupamento_mamadas_proximas():
    pass

def processar_mamadeira():
    pass

def processar_alimentcao_solida():
    pass

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
resLeite = None

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
    else:
        #AMEC estimator?
        pass
    leiteMaterno_mesclado = pandas.concat([resLeite, dfM], ignore_index=True)
    #agrupar leiteMaterno_mesclado por data
    horarios_leitem = leiteMaterno_mesclado['dhInicio']
    leiteMaterno_mesclado['dh'].fillna(horarios_leitem, inplace=True)
    leiteMaterno_mesclado['data'] = leiteMaterno_mesclado['dh'].date
    leiteMaterno_mesclado['hora'] = leiteMaterno_mesclado['dh'].time

    #para cada DF do agrupamento, montar uma list contendo a quantidade de mamadas encontradas
    qtd_mamadas = leiteMaterno_mesclado.groupby('data').count().apply(numpy.average,0) #seria melhor enviar a quantidade exata das mamadas do dia para o estimador, mas vamos simplificar por hora com a média

    estima_volume_nMamadas = leiteMaterno_mesclado.apply('estimador_amamentacao_nMamadas', 1, False, 'reduce', {'nMamadas': qtd_mamadas}) #mesclar os resultados quando se trata de mamadas em mamadeira antes de aplicar a função

    #comparar resultados e juntar os melhores ao dfM ==> no caso, vou partir para a média novamente
    resultados_estimadores = pandas.DataFrame([estima_volume_amex, estima_volume_nMamadas])
    resultados_estimadores['data'] = resultados_estimadores['dh'].date
    resultados_estimadores['hora'] = resultados_estimadores['dh'].time
    resultados_estimadores.drop('dh',1)
    resultados_estimadores.groupby('data').agg({'volume': numpy.average})

    #interpolar os resultados presentes para deduzir os pontos de mamada ausentes. Utilizar métodos lineares para a dedução
    #plotar: volumes (barras) e scatter (horarios); as mamadeiras em legenda especial no caso do scatter
    leiteMaterno_mesclado.plot(x='dh', y='volume', kind='scatter', use_index=False)
    resultados_estimadores.plot(x='data', y='volume', kind='bar', use_index=False)
    #invocar o matplotlib para salvar as imagens

if nMamadeira > 0:
    #retirar resultados contendo leite materno
    if resLeite != None:
        dfM.drop(dfM['alimento'] == 'LEITEM', inplace=True)
    #seaprar os tipos de alimentos disponíveis em df ou grupos (o que por sua vez já permite pegar os dataframes separados)
    #criar tabelas-somatório com as médias diárias de consumo de cada um dos tipos de alimentos encontrados
    dfM.groupby(['dh','alimento']).agg({'volume': numpy.average})
    #imprimir as tabelas

if nSolido > 0:
    #usando o NLTK, tentar encontrar ingredientes das refeições aqui incluídas e fazer um listagem da frequência durante o período apresentado
    pass