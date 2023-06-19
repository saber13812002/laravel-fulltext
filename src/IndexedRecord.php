<?php

namespace Saber13812002\Laravel\Fulltext;

use Illuminate\Database\Eloquent\Model;

class IndexedRecord extends Model
{
    protected $table = 'laravel_fulltext';

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
        $YehArabic = 'ی';
        $YehPersian = 'ي';
        $KafArabic = 'ک';
        $KafPersian = 'ك';
        $normalizedPhrase = "";

        for ($i = 0; $i < str_len($phrase); $i++) {
            $skip = false;
            switch ($phrase[i]) {
                case $TATWEEL:
                case $KASRATAN:
                case $DAMMATAN:
                case $FATHATAN:
                case $FATHA:
                case $DAMMA:
                case $KASRA:
                case $SHADDA:
                case $SUKUN:
                case $Fathe:
                case $Zamme:
                case $Kasre:
                case $TanvinFathe:
                case $TanvinZamme:
                case $TanvinKasre:
                case $Tashdid:
                case $Sokun:
                case $HamzeJoda:
                case $KhateTire:
                    $skip = true;
                default:
                    if (!$skip) {
                        $normalizedPhrase .= $phrase[i];
                    } elseif ($phrase[i] == $YehArabic) {
                        $normalizedPhrase .= $YehPersian;
                    } elseif ($phrase[i] == $KafArabic) {
                        $normalizedPhrase .= $KafPersian;
                    }
                    break;
            }
        }

        return $normalizedPhrase;
    }
}
