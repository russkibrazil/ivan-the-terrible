import nltk
import argparse

nltk.download('wordnet')

parser = argparse.ArgumentParser()
parser.add_argument("pesquisa", type=str)
args = parser.parse_args()

pesquisa = (args.pesquisa).split(' ')
WNlemma = nltk.snowball.PortugueseStemmer(True)
stop_words = nltk.corpus.stopwords.words('portuguese')
filtered_words = [WNlemma.stem(word) for word in pesquisa if word not in stop_words]
print(filtered_words)
