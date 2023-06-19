<?php

namespace Saber13812002\Laravel\Fulltext;

use Illuminate\Database\Eloquent\Model;

class IndexedRecord extends Model
{
    protected $table = 'laravel_fulltext';
    private string $analyzer = "persian";

    public function __construct(array $attributes = [])
    {
        $this->connection = config('laravel-fulltext.db_connection');

        parent::__construct($attributes);
    }

    public function indexable()
    {
        return $this->morphTo();
    }

    public function updateIndex()
    {
        $this->setAttribute('indexed_title', $this->analyzer ? $this->runAnalyzer($this->indexable->getIndexTitle()) : $this->indexable->getIndexTitle());
        $this->setAttribute('indexed_content', $this->analyzer ? $this->runAnalyzer($this->indexable->getIndexContent()) : $this->indexable->getIndexContent());
        $this->save();
    }

    private function runAnalyzer($phrase)
    {
        return self::normalize($phrase);
    }


    /**
     * @param string $phrase
     * @return string
     */
    public static function normalize(string $phrase): string
    {
        $TATWEEL = '\u0640';
        $FATHATAN = '\u064B';
        $DAMMATAN = '\u064C';
        $KASRATAN = '\u064D';
        $FATHA = '\u064E';
        $DAMMA = '\u064F';
        $KASRA = '\u0650';
        $SHADDA = '\u0651';
        $SUKUN = '\u0652';
        $Fathe = 'َ';
        $Zamme = 'ُ';
        $Kasre = 'ِ';
        $TanvinFathe = 'ً';
        $TanvinZamme = 'ٌ';
        $TanvinKasre = 'ٍ';
        $Tashdid = 'ّ';
        $Sokun = 'ْ';
        $HamzeJoda = 'ء';
        $KhateTire = 'ـ';


        $remove = array('ِ', 'ُ', 'ٓ', 'ٰ', 'ْ', 'ٌ', 'ٍ', 'ً', 'ّ', 'َ', $KhateTire, $TATWEEL,
            $FATHATAN, $DAMMATAN, $KASRATAN, $FATHA, $DAMMA, $KASRA,
            $SHADDA, $SUKUN, $Fathe, $Zamme, $Kasre, $TanvinFathe, $TanvinZamme,
            $TanvinKasre, $Tashdid, $Sokun, $HamzeJoda);

        $phrase = str_replace($remove, '', $phrase);

        $YehArabic = 'ی';
        $YehPersian = 'ي';
        $KafArabic = 'ک';
        $KafPersian = 'ك';

        $replace = array($YehArabic);
        $phrase = str_replace($replace, $YehPersian, $phrase);

        $replace = array($KafArabic);
        $phrase = str_replace($replace, $KafPersian, $phrase);

        return $phrase;
    }
}
