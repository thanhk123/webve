<?php

/**
 * @package php-font-lib
 * @link    https://github.com/PhenX/php-font-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
namespace DynamicOOOS\FontLib\Table;

use DynamicOOOS\FontLib\TrueType\File;
use DynamicOOOS\FontLib\Font;
use DynamicOOOS\FontLib\BinaryStream;
/**
 * Generic Font table directory entry.
 *
 * @package php-font-lib
 */
class DirectoryEntry extends BinaryStream
{
    /**
     * @var File
     */
    protected $font;
    /**
     * @var Table
     */
    protected $font_table;
    public $entryLength = 4;
    public $tag;
    public $checksum;
    public $offset;
    public $length;
    protected $origF;
    /**
     * @param string $data
     *
     * @return int
     */
    static function computeChecksum($data)
    {
        $len = \mb_strlen($data, '8bit');
        $mod = $len % 4;
        if ($mod) {
            $data = \str_pad($data, $len + (4 - $mod), "\x00");
        }
        $len = \mb_strlen($data, '8bit');
        $hi = 0x0;
        $lo = 0x0;
        for ($i = 0; $i < $len; $i += 4) {
            $hi += (\ord($data[$i]) << 8) + \ord($data[$i + 1]);
            $lo += (\ord($data[$i + 2]) << 8) + \ord($data[$i + 3]);
            $hi += $lo >> 16;
            $lo = $lo & 0xffff;
            $hi = $hi & 0xffff;
        }
        return ($hi << 8) + $lo;
    }
    function __construct(File $font)
    {
        $this->font = $font;
        $this->f = $font->f;
    }
    function parse()
    {
        $this->tag = $this->font->read(4);
    }
    function open($filename, $mode = self::modeRead)
    {
        // void
    }
    function setTable(Table $font_table)
    {
        $this->font_table = $font_table;
    }
    function encode($entry_offset)
    {
        Font::d("\n==== {$this->tag} ====");
        //Font::d("Entry offset  = $entry_offset");
        $data = $this->font_table;
        $font = $this->font;
        $table_offset = $font->pos();
        $this->offset = $table_offset;
        $table_length = $data->encode();
        $font->seek($table_offset);
        $table_data = $font->read($table_length);
        $font->seek($entry_offset);
        $font->write($this->tag, 4);
        $font->writeUInt32(self::computeChecksum($table_data));
        $font->writeUInt32($table_offset);
        $font->writeUInt32($table_length);
        Font::d("Bytes written = {$table_length}");
        $font->seek($table_offset + $table_length);
    }
    /**
     * @return File
     */
    function getFont()
    {
        return $this->font;
    }
    function startRead()
    {
        $this->font->seek($this->offset);
    }
    function endRead()
    {
        //
    }
    function startWrite()
    {
        $this->font->seek($this->offset);
    }
    function endWrite()
    {
        //
    }
}
