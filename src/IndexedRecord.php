<?php

namespace Saber13812002\Laravel\Fulltext;

use Illuminate\Database\Eloquent\Model;

class IndexedRecord extends Model
{
    protected $table = 'laravel_fulltext';
    private string $analyzer = 'persian';

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
        $this->setAttribute('indexed_title', $this->indexable->getIndexTitle());
        $this->setAttribute('indexed_content', $this->analyzer ? self::runAnalyzer($this->indexable->getIndexContent()) : $this->indexable->getIndexContent());
        $this->save();
    }

    public static function runAnalyzer($phrase): string
    {
        $originalPhrase = $phrase;

        [$isChangedInNormalize, $phrase] = self::normalize($phrase, true);
        $phraseIfChanged = ($isChangedInNormalize ? $phrase : '');

        //        dd($phrase);

        [$isChangedExtraKeyword, $extraKeywords] = self::addKeywordsPhrase($originalPhrase);
        //        dd($extraKeywords);
        //        dd($phraseIfChanged);
        $returnPhrase = '';

        if ($isChangedExtraKeyword) {
            $returnPhrase = $originalPhrase.' '.$extraKeywords.' '.$phraseIfChanged;
        } else {
            if ($originalPhrase == $phraseIfChanged) {
                $returnPhrase = $originalPhrase;
            } else {
                $returnPhrase = $originalPhrase.' '.$phraseIfChanged;
            }
        }
        //        dd($returnPhrase);
        return $returnPhrase;
    }

    /**
     * @return array
     */
    public static function normalize(string $phrase, bool $needChange = false)
    {
        $isChanged = false;

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

        $remove = [
            'ِ', 'ُ', 'ٓ', 'ٰ', 'ْ', 'ٌ', 'ٍ', 'ً', 'ّ', 'َ', $KhateTire, $TATWEEL,
            $FATHATAN, $DAMMATAN, $KASRATAN, $FATHA, $DAMMA, $KASRA,
            $SHADDA, $SUKUN, $Fathe, $Zamme, $Kasre, $TanvinFathe, $TanvinZamme,
            $TanvinKasre, $Tashdid, $Sokun, $HamzeJoda,
        ];

        $oldPhrase = $phrase;
        $newPhrase = str_replace($remove, '', $phrase);
        $phrase = $oldPhrase == $newPhrase ? $oldPhrase : $newPhrase;
        if ($oldPhrase != $newPhrase) {
            $isChanged = true;
        }

        $YehArabic = 'ی';
        $YehPersian = 'ي';
        $KafArabic = 'ک';
        $KafPersian = 'ك';

        $replace = [$YehArabic];
        $oldPhrase = $phrase;
        $newPhrase = str_replace($replace, $YehPersian, $phrase);
        $phrase = $oldPhrase == $newPhrase ? $oldPhrase : $newPhrase;
        if ($oldPhrase != $newPhrase) {
            $isChanged = true;
        }

        $replace = [$KafArabic];
        $oldPhrase = $phrase;
        $newPhrase = str_replace($replace, $KafPersian, $phrase);
        $phrase = $oldPhrase == $newPhrase ? $oldPhrase : $newPhrase;
        if ($oldPhrase != $newPhrase) {
            $isChanged = true;
        }

        $AlaArabic = "الأ";
        $AlaPersian = "الا";

        $replace = [$AlaArabic];
        $oldPhrase = $phrase;
        $newPhrase = str_replace($replace, $AlaPersian, $phrase);
        $phrase = $oldPhrase == $newPhrase ? $oldPhrase : $newPhrase;
        if ($oldPhrase != $newPhrase) {
            $isChanged = true;
        }

        if ($needChange) {
            return [$isChanged, $phrase];
        }

        return $phrase;
    }

    public static function addKeywordsPhrase(string $originalPhrase): array
    {
        $extraKeywords = '';
        $isChanged = false;

        $HaaHavvaz = 'ه';
        $TaaTanisArabic = 'ة';
        $TaaPersian = 'ت';

        if (self::find($TaaTanisArabic, $originalPhrase)) {
            $isChanged = true;
            $replace = [$TaaTanisArabic];
            $extraKeywords .= ' '.str_replace($replace, $HaaHavvaz, $originalPhrase);
            $extraKeywords .= ' '.str_replace($replace, $TaaPersian, $originalPhrase);
        }

        $Alef = 'ا';
        $VavTaaTanisArabic = 'وة';

        //        dd($VavTaaTanisArabic, $originalPhrase, self::find($VavTaaTanisArabic, $originalPhrase));
        if (self::find($VavTaaTanisArabic, $originalPhrase)) {
            $isChanged = true;
            $replace = [$VavTaaTanisArabic];
            $extraKeywords .= ' '.str_replace($replace, $Alef.$TaaPersian, $originalPhrase);
        }
        //        dd($extraKeywords);

        $YaaAlefMaghsoore = 'یٰ';
        $AlefMaghsoore = 'ٰ';
        $Alef = 'ا';

        if (self::find($YaaAlefMaghsoore, $originalPhrase)) {
            $isChanged = true;
            $replace = [$YaaAlefMaghsoore];
            $extraKeywords .= ' '.str_replace($replace, $Alef, $originalPhrase);
        }

        if (self::find($AlefMaghsoore, $originalPhrase)) {
            $isChanged = true;
            $replace = [$AlefMaghsoore];
            $extraKeywords .= ' '.str_replace($replace, $Alef, $originalPhrase);
        }

        return [$isChanged, $extraKeywords];
    }

    public static function find($character, $phrase): bool
    {
        //        dd($character, $phrase, str_contains($character, $phrase));
        if (str_contains($phrase, $character)) {
            return true;
        }

        return false;
    }
}
