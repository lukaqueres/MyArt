<?php
namespace Web\Models;

class File {
    public string $key;
    public string $name;
    public string $type;
    public string $extension;
    public string $tmp_name;
    public int $error;
    public int $size;
    public bool $empty = false;

    public function __construct($key, $file, bool $empty = false) {
        if ( $empty ) {
            $this->empty = true;
        }
        $this->key = $key;
        $this->name = $file["name"];
        $this->type = $file["type"];
        $this->extension = pathinfo( $file["name"], PATHINFO_EXTENSION );
        $this->tmp_name = $file["tmp_name"];
        $this->error = $file["error"];
        $this->size = $file["size"];

        if ( $this->tmp_name == "" ) {
			$this->empty = true;
		}
    }

    public function move($destination, $root = true, $name = Null ): bool {
        $name = $name?$name:$this->name;
        $path = $root?$GLOBALS['rootPath']:"";
        if ( !str_ends_with($destination, "/") ) { $destination .= "/"; };
        if ( !str_starts_with($destination, "/") ) { $destination = "/" . $destination; };
        $path .= "{$destination}{$name}.{$this->extension}";
        $status = move_uploaded_file($this->tmp_name, $path);
        return $status;
    }

    public static function exists($destination, $root=true) {
        $path = $root?$GLOBALS['rootPath']:"";
        if ( !str_ends_with($destination, "/") ) { $destination .= "/"; };
        if ( !str_starts_with($destination, "/") ) { $destination = "/" . $destination; };
        $path .= "{$path}{$destination}";
        if ( file_exists($path)) {
            return true;
        } else {
            return false;
        }
    }
}