<?php
/******************************************************************************
 * Klasse erweitert das PHP-DateTime-Objekt um einige nuetzliche Funktionen
 *
 * Copyright    : (c) 2004 - 2015 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * In dieser Klasse werden hauptsaechlich Funktionen eingebaut, die erst ab
 * PHP 5.3.0 zur Verfuegung stehen. So kann ein beliebig formatiertes Datum
 * im Konstruktor uebergeben werden und daraus wird ein Datetime-Objekt erzeugt.
 *
 * The following functions are available:
 *
 * getAge() - berechnet aus dem Datum das Alter einer Person
 * getDateTimeEnglish()
 *          - liefert das gesetzte DateTime im Format 'Y-m-d H:i:s' zurueck
 * getTimestamp() - gibt den Unix-Timestamp zurueck
 * setDateTime($date, $format)
 *          - setzt das Datum und die Uhrzeit fuer das aktuelle Objekt
 * valid()  - gibt true oder false zurueck, je nachdem ob DateTime gueltig ist
 *
 *****************************************************************************/

class DateTimeExtended extends DateTime
{
    private $errorCode;
    private $year, $month, $day, $hour, $minute, $second;
    protected $format; ///< The format of the date of this element. The syntax is similar to PHP date()

    /**
     * es muss das Datum und das dazugehoerige Format uebergeben werden
     * @param string $date   String mit dem Datum
     * @param string $format das zum Datum passende Format (Schreibweise aus date())
     * @param string $type   'datetime', 'date' oder 'time'
     */
    public function __construct($date, $format, $type = 'datetime')
    {
        $this->weekdays = array();
        $this->year     = 0;
        $this->month    = 0;
        $this->day      = 0;
        $this->hour     = 0;
        $this->minute   = 0;
        $this->second   = 0;
        $this->format   = $format;

        // je nach Type das Format erweitern, da nur Datetime verarbeitet werden kann
        if($type === 'date')
        {
            $this->setDateTime($date.' 01:00:00', $format.' h:i:s');
        }
        elseif($type === 'time')
        {
            $this->setDateTime('2000-01-01 '.$date, 'Y-m-d '.$format);
        }
        else
        {
            $this->setDateTime($date, $format);
        }

        parent::__construct($this->getDateTimeEnglish());
    }

    /**
     * berechnet aus dem Datum das Alter einer Person
     * @return int
     */
    public function getAge()
    {
        // Alter berechnen
        // Hier muss man aufpassen, da viele PHP-Funkionen nicht mit einem Datum vor 1970 umgehen koennen !!!
        $act_date = getDate(time());
        $birthday = false;

        if($act_date['mon'] >= $this->month)
        {
            if($act_date['mon'] == $this->month)
            {
                if($act_date['mday'] >= $this->day)
                {
                    $birthday = true;
                }
            }
            else
            {
                $birthday = true;
            }
        }
        $age = $act_date['year'] - $this->year;
        if(!$birthday)
        {
            $age--;
        }

        return $age;
    }

    /**
     * The method will convert a date format with the syntax of date()
     * to a syntax that is known by the bootstrap datepicker plugin.
     * e.g.: input: 'd.m.Y' output: 'dd.mm.yyyy'
     * e.g.: input: 'j.n.y' output: 'd.m.yy'
     * @param  string $format Optional a format could be given in the date() syntax that should be transformed.
     *                        If no format is set then the format of the class constructor will be used.
     * @return string Return the transformed format that is valid for the datepicker.
     */
    public static function getDateFormatForDatepicker($format = null)
    {
        if($format === null)
        {
            $format = $this->format;
        }

        $formatLength = strlen($format);
        $destFormat   = '';

        for($position = 0; $position < $formatLength; $position++)
        {
            $formatChar = substr($format, $position, 1);

            switch($formatChar)
            {
                case 'd':
                    $destFormat .= 'dd';
                    break;
                case 'j':
                    $destFormat .= 'd';
                    break;
               case 'l':
                    $destFormat .= 'DD';
                    break;
                case 'D':
                    $destFormat .= 'D';
                    break;
                case 'm':
                    $destFormat .= 'mm';
                    break;
                case 'n':
                    $destFormat .= 'm';
                    break;
               case 'F':
                    $destFormat .= 'MM';
                    break;
               case 'M':
                    $destFormat .= 'M';
                    break;
               case 'Y':
                    $destFormat .= 'yyyy';
                    break;
               case 'y':
                    $destFormat .= 'yy';
                    break;
                default:
                    $destFormat .= $formatChar;
                    break;
            }
        }

        return $destFormat;
    }

    /**
     * liefert das gesetzte DateTime im Format 'Y-m-d H:i:s' zurueck
     * @return string
     */
    public function getDateTimeEnglish()
    {
        return $this->year.'-'.$this->month.'-'.$this->day.' '.$this->hour.':'.$this->minute.':'.$this->second;
    }

    /**
     * gibt den Unix-Timestamp zurueck
     * @return string
     */
    public function getTimestamp()
    {
        return $this->format('U');
    }

    /**
     * Returns an array with all 7 weekdays with full name in the specific language.
     * @param  int             $weekday The number of the weekday for which the name should be returned (1 = Monday ...)
     * @return string|string[] with all 7 weekday or if param weekday is set than the full name of that weekday
     */
    public static function getWeekdays($weekday = 0)
    {
        global $gL10n;

        $weekdays = array(1 => $gL10n->get('SYS_MONDAY'),
                          2 => $gL10n->get('SYS_TUESDAY'),
                          3 => $gL10n->get('SYS_WEDNESDAY'),
                          4 => $gL10n->get('SYS_THURSDAY'),
                          5 => $gL10n->get('SYS_FRIDAY'),
                          6 => $gL10n->get('SYS_SATURDAY'),
                          7 => $gL10n->get('SYS_SUNDAY'));

        if($weekday > 0)
        {
            return $weekdays[$weekday];
        }
        else
        {
            return $weekdays;
        }
    }

    /**
     * Method will replace a valid php date or time format into a valid regex syntax.
     * This method to not accept the complete format string.
     * Only a segment of the format can be given to this method, so you
     * must call this method several times to get the whole format.
     * @param  array  $formatArray An array with a part of a datetime format e.g. $array(0 => 'd')
     * @return string Returns the regex for the format array element e.g. (?P<d>0[1-9]|[12][0-9]|3[01])
     */
    public static function replaceDatetimeFormatIntoRegex($formatArray)
    {
        $formatPatterns = array(
            'a' => '(?P<a>am|pm)',
            'A' => '(?P<A>AM|PM)',
            'B' => '(?P<B>[0-9]{3})',
//            'c' => '(?P<c>)',
            'd' => '(?P<d>0[1-9]|[12][0-9]|3[01])',
            'D' => '(?P<D>Mon|Tue|Wed|Thu|Fri|Sat|Sun)',
            'F' => '(?P<F>January|February|March|April|May|June|July|August|September|October|November|December)',
            'g' => '(?P<g>[1-9]|1[0-2])',
            'G' => '(?P<G>[0-9]|1[0-9]|2[0-3])',
            'h' => '(?P<h>0[1-9]|1[0-2])',
            'H' => '(?P<H>[01][0-9]|2[0-3])',
            'i' => '(?P<i>[0-4][0-9]|5[0-9])',
            'I' => '(?P<I>[01])',
            'j' => '(?P<j>[1-9]|[12][0-9]|3[01])',
            'l' => '(?P<l>Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday)',
            'L' => '(?P<L>[01])',
            'm' => '(?P<m>0[1-9]|1[0-2])',
            'M' => '(?P<M>Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)',
            'n' => '(?P<n>[1-9]|1[0-2])',
            'O' => '(?P<O>[+\-][0-9]{4})',
//            'r' => '(?P<r>)',
            's' => '(?P<s>[0-4][0-9]|5[0-9])',
            'S' => '(?P<S>st|nd|rd|th)',
            't' => '(?P<t>28|29|30|31)',
            'T' => '(?P<T>[A-Z]{3})',
            'U' => '(?P<U>[1-9][0-9]*)',
            'w' => '(?P<w>[0-6])',
            'W' => '(?P<W>[1-9]|[1-4][0-9]|5[0-3])',
            'Y' => '(?P<Y>[0-9]{4})',
            'y' => '(?P<y>[0-9]{2})',
            'z' => '(?P<z>[0-9]|[12][0-9][0-9]|3(?:[0-5][0-9]|6[0-5]))',
            'Z' => '(?P<Z>-?(?:[0-9]|[1-9][0-9]{3}|[1-3][0-9]{4}|4(?:[0-2][0-9]{3}|3[01][0-9]{2}|3200)))',
        );

        if(isset($formatPatterns[$formatArray[0]]))
        {
            return $formatPatterns[$formatArray[0]];
        }
        else
        {
            return $formatArray[0];
        }
    }

    /**
     * setzt das Datum und die Uhrzeit fuer das aktuelle Objekt
     * erwartet wird ein String und das dazugehoerige Format aehnlich date()
     * @param string $date
     * @param string $format
     */
    public function setDateTime($date, $format)
    {
        $this->year      = 0;
        $this->month     = 0;
        $this->day       = 0;
        $this->hour      = 0;
        $this->minute    = 0;
        $this->second    = 0;
        $this->errorCode = true;

        $regexp = preg_replace_callback('/[a-zA-Z]/', 'DateTimeExtended::replaceDatetimeFormatIntoRegex', $format);

        if (preg_match('/^'.$regexp.'$/', trim($date), $match))
        {
            foreach ($match as $format => $value)
            {
                switch ($format) {
                    case 'g':
                    case 'G':
                    case 'h':
                    case 'H':
                        $this->hour = $value;
                        break;
                    case 'i':
                        $this->minute = $value;
                        break;
                    case 's':
                        $this->second = $value;
                        break;
                    case 'm':
                    case 'n':
                        $this->month = $value;
                        break;
                    case 'd':
                    case 'j':
                        $this->day = $value;
                        break;
                    case 'Y':
                    case 'y':
                        $this->year = $value;
                        break;
                }
            }
        }

        // Datum validieren
        if($this->month === 0 || $this->day === 0)
        {
            $this->errorCode = false;
        }
    }

    /**
     * gibt true oder false zurueck, je nachdem ob DateTime gueltig ist
     * @return bool
     */
    public function valid()
    {
        return $this->errorCode;
    }
}
?>
