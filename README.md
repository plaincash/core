# PlainCash Core

### LevelDB

**Links:**
- https://github.com/reeze/php-leveldb

**Install:**
1. Get extension.
   - From PECL:
      ```
      pecl install leveldb-0.2.1
      ```
   - From sources:
      ```$xslt
      $ git clone https://github.com/reeze/php-leveldb.git
      $ cd php-leveldb
      $ phpize
      $ ./configure --with-leveldb=/path/to/your/leveldb-1.*.*
      $ make
      $ make install
      ```
2. Add "extension=leveldb.so" to php.ini.
