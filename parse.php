<?php
  class Lexical_Analyzer {

    /// @var Defines regex for matching .IPPcode19 header.
    const R_HEADER = '/^\.ippcode19[\t ]*(\#.*)?$/i';

    // ### Definitions of regular expression detecting instruction OP Codes.

    /// @var Defines regex for matching empty lines (or with comments).
    const R_EMPTY = '/^[\t\f ]*(\#.*)?$/';

    /// @var Defines regex for detecting instruction # MOVE〈var〉〈symb〉
    const R_MOVE = '/^\s*MOVE\s+.*/i';

    /// @var Defines regex for detecting instruction # CREATEFRAME
    const R_CREATEFRAME = '/^\s*CREATEFRAME.*/i';

    /// @var Defines regex for detecting instruction # PUSHFRAME
    const R_PUSHFRAME = '/^\s*PUSHFRAME.*/i';

    /// @var Defines regex for detecting instruction # POPFRAME
    const R_POPFRAME = '/^\s*POPFRAME.*/i';

    /// @var Defines regex for detecting instruction # DEFVAR〈var〉
    const R_DEFVAR = '/^\s*DEFVAR\s+.*/i';

    /// @var Defines regex for detecting instruction # CALL〈label〉
    const R_CALL = '/^\s*CALL\s+.*/i';

    /// @var Defines regex for detecting instruction # RETURN
    const R_RETURN = '/^\s*RETURN.*/i';

    /// @var Defines regex for detecting instruction # PUSHS〈symb〉
    const R_PUSHS = '/^\s*PUSHS\s+.*/i';

    /// @var Defines regex for detecting instruction # POPS〈var〉
    const R_POPS = '/^\s*POPS\s+.*/i';

    /// @var Defines regex for detecting instruction # ADD〈var〉〈symb1〉〈symb2〉
    const R_ADD = '/^\s*ADD\s+.*/i';

    /// @var Defines regex for detecting instruction # SUB〈var〉〈symb1〉〈symb2〉
    const R_SUB = '/^\s*SUB\s+.*/i';

    /// @var Defines regex for detecting instruction # MUL〈var〉〈symb1〉〈symb2〉
    const R_MUL = '/^\s*MUL\s+.*/i';

    /// @var Defines regex for detecting instruction # IDIV〈var〉〈symb1〉〈symb2〉
    const R_IDIV = '/^\s*IDIV\s+.*/i';

    /// @var Defines regex for detecting instruction # LT〈var〉〈symb1〉〈symb2〉
    const R_LT = '/^\s*LT\s+.*/i';

    /// @var Defines regex for detecting instruction # GT〈var〉〈symb1〉〈symb2〉
    const R_GT = '/^\s*GT\s+.*/i';

    /// @var Defines regex for detecting instruction # EQ〈var〉〈symb1〉〈symb2〉
    const R_EQ = '/^\s*EQ\s+.*/i';

    /// @var Defines regex for detecting instruction # AND〈var〉〈symb1〉〈symb2〉
    const R_AND = '/^\s*AND\s+.*/i';

    /// @var Defines regex for detecting instruction # OR〈var〉〈symb1〉〈symb2〉
    const R_OR = '/^\s*OR\s+.*/i';

    /// @var Defines regex for detecting instruction # NOT〈var〉〈symb1〉〈symb2〉
    const R_NOT = '/^\s*NOT\s+.*/i';

    /// @var Defines regex for detecting instruction # INT2CHAR〈var〉〈symb〉
    const R_INT2CHAR = '/^\s*INT2CHAR\s+.*/i';

    /// @var Defines regex for detecting instruction # STRI2INT〈var〉〈symb1〉〈symb2〉
    const R_STRI2INT = '/^\s*STRI2INT\s+.*/i';

    /// @var Defines regex for detecting instruction # READ〈var〉〈type〉
    const R_READ = '/^\s*READ\s+.*/i';

    /// @var Defines regex for detecting instruction # WRITE〈symb〉
    const R_WRITE = '/^\s*WRITE\s+.*/i';

    /// @var Defines regex for detecting instruction # CONCAT〈var〉〈symb1〉〈symb2〉
    const R_CONCAT = '/^\s*CONCAT\s+.*/i';

    /// @var Defines regex for detecting instruction # STRLEN〈var〉〈symb〉
    const R_STRLEN = '/^\s*STRLEN\s+.*/i';

    /// @var Defines regex for detecting instruction # GETCHAR〈var〉〈symb1〉〈symb2〉
    const R_GETCHAR = '/^\s*GETCHAR\s+.*/i';

    /// @var Defines regex for detecting instruction # SETCHAR〈var〉〈symb1〉〈symb2〉
    const R_SETCHAR = '/^\s*SETCHAR\s+.*/i';

    /// @var Defines regex for detecting instruction # TYPE〈var〉〈symb〉
    const R_TYPE = '/^\s*TYPE\s+.*/i';

    /// @var Defines regex for detecting instruction # LABEL〈label〉
    const R_LABEL = '/^\s*LABEL\s+.*/i';

    /// @var Defines regex for detecting instruction # JUMP〈label〉
    const R_JUMP = '/^\s*JUMP\s+.*/i';

    /// @var Defines regex for detecting instruction # JUMPIFEQ〈label〉〈symb1〉〈symb2〉
    const R_JUMPIFEQ = '/^\s*JUMPIFEQ\s+.*/i';

    /// @var Defines regex for detecting instruction # JUMPIFNEQ〈label〉〈symb1〉〈symb2〉
    const R_JUMPIFNEQ = '/^\s*JUMPIFNEQ\s+.*/i';

    /// @var Defines regex for detecting instruction # EXIT〈symb〉
    const R_EXIT = '/^\s*EXIT\s+.*/i';

    /// @var Defines regex for detecting instruction # DPRINT〈symb〉
    const R_DPRINT = '/^\s*DPRINT\s+.*/i';

    /// @var Defines regex for detecting instruction # BREAK
    const R_BREAK = '/^\s*BREAK.*/i';


    // ### Definitions of regular expressions detecting instruction operands.

    /// @var Defines regex for matching whitespace between instructions and operands. Doesn't match newlines.
    const R_WHITESPACE = '/[\t\f ]+/';

    /// @var Defines regex for matching variable operands.
    const R_VAR = '/[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*/';

    /// @var Defines regex for matching variable or literal operands.
    const R_SYMB = '/([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(0|(\+|-)?[1-9][0-9]*)|bool@(true|false)|string@([^\\\s#]|\\[0-9][0-9][0-9])*|nil@nil)/';

    /// @var Defines regex for matching variable or integer literal operands.
    const R_SYMB_INT = '/([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(0|(\+|-)?[1-9][0-9]*))/';

    /// @var Defines regex for matching variable or string literal operands.
    const R_SYMB_STRING = '/([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|string@([^\\\s#]|\\[0-9][0-9][0-9])*)/';

    /// @var Defines regex for matching label operands.
    const R_LABELARG = '/[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*\s*/';

    /// @var Defines regex for matching type operands.
    const R_TYPEARG = '/(int|bool|string|nil)\s*/';

    /// @var Defines regex for matching comments.
    const R_COMMENT = '/\#.*/';

    /// Analyzes a line of code detecting instructions and their operands,
    /// checks whether they are syntacticly correct.
    /// Syntacticaly correct constructions writes into an xml document.
    /// @param $line  The line of code being analyzed.
    /// @param $xmlW  The XML Writer object used to write the xml document.
    function analyze_instruction($line, $xmlm) {
      // Splits the line into an array of tokens.
      $tokens = preg_split(self::R_WHITESPACE, $line);
      $token_count = count($tokens);

      // Analyzes the line itself and subsequently all the tokens.
      if (preg_match_all(self::R_EMPTY, $line)) {
        // do nothing
        return;
      } elseif (preg_match_all(self::R_MOVE, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('MOVE', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_CREATEFRAME, $line)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('CREATEFRAME');
        else exit(23);
      } elseif (preg_match_all(self::R_PUSHFRAME, $line)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('PUSHFRAME');
        else exit(23);
      } elseif (preg_match_all(self::R_POPFRAME, $line)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('POPFRAME');
        else exit(23);
      } elseif (preg_match_all(self::R_DEFVAR, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('DEFVAR', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_CALL, $line)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('CALL', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_RETURN, $line)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('RETURN');
        else exit(23);
      } elseif (preg_match_all(self::R_PUSHS, $line)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('PUSHS', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_POPS, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('POPS', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_ADD, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('ADD', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_SUB, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('SUB', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_MUL, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('MUL', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_IDIV, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('IDIV', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_LT, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('LT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_GT, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('GT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_EQ, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('EQ', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_AND, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('AND', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_OR, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('OR', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_NOT, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('NOT', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_INT2CHAR, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('INT2CHAR', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_STRI2INT, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('STRI2INT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_READ, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_TYPEARG, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('READ', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_WRITE, $line)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('WRITE', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_CONCAT, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && preg_match_all(self::R_SYMB_STRING, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('CONCAT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_STRLEN, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('STRLEN', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_GETCHAR, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('GETCHAR', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_SETCHAR, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_STRING, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('SETCHAR', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_TYPE, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('TYPE', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_LABEL, $line)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('LABEL', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_JUMP, $line)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('JUMP', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_JUMPIFEQ, $line)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('JUMPIFEQ', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_JUMPIFNEQ, $line)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('JUMPIFNEQ', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_EXIT, $line)) {
        if (preg_match_all(self::R_SYMB_INT, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('EXIT', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_DPRINT, $line)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('DPRINT', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_BREAK, $line)) {
        if (($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1])))
  			   $xmlm->write_instruction('BREAK');
        else exit(23);
      } else exit(22);
    }

    /// Analyzes input from file line by line and encodes it in XML.
    /// @param $stdin Input file.
    /// @param $xmlm  XML manager used to write the XML code.
    function main_loop($stdin, $xmlm) {
      $i = 0;
      //echo $i . ' ';
      $i = $i + 1;
      $line = '';

      while (($line = fgets($stdin, 4096)) !== false) {
        //echo $i . ' ';
        $line = trim($line);
        if (preg_match_all('/.*endthefuckingfile.*/', $line))
          break;
        self::analyze_instruction($line, $xmlm);
        $i = $i + 1;
      }
    }

    /// Checks for .ippcode19 file header.
    /// If the header is not found or is in an incorrect format,
    /// application exits with exit code 21.
    function check_for_header($stdin) {
      $line = trim(fgets($stdin));
      if (preg_match_all(self::R_HEADER, $line) == false)
        exit(21);
    }
  }


  class XML_Manager {
    private const ERR_NOTINIT = 'Error - XML Writer is not initalized!';
    private const ERR_REINIT = 'Error - XML Writer is already initialized';

    private const R_VAR = '/(LF|GF|TF)@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*/';
    private const R_INT = '/int@(+|-)?[1-9][0-9]*/';
    private const R_BOOL = '/bool@(true|false)/';
    private const R_STRING = '/string@([^\\\s#]|\\[0-9][0-9][0-9])/';
    private const R_NIL = '/nil@nil/';
    private const R_ARGSPLIT = '/@/';

    private $errf = null;

    private $instruction_count = 1;

    private $writer = null;

    function __construct() {
      $this->errf = fopen('php://stderr', 'w');
    }

    /// Initializes XML writer memory.
    /// Starts the document and main element 'program'.
    function init() {
      if ($this->writer == null) {
        $this->writer = new XMLWriter();
        $this->writer->openURI('php://output');
        $this->writer->setIndent(1);
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->writer->startElement('program');
        $this->writer->startAttribute('language');
        $this->writer->text('IPPcode19');
        $this->writer->endAttribute();
      } else {
        fwrite($this->errf, ERR_REINIT);
        exit (99);
      }
    }

    /// Writes a single instruction and all of it's parameters into XML writer memory.
    /// @param $OPcode    OPcode of the instruction.
    /// @param $arg1      String optionally containing IPPcode19 representation of the first argument.
    /// @param $arg2      String optionally containing IPPcode19 representation of the second argument.
    /// @param $arg3      String optionally containing IPPcode19 representation of the third argument.
    function write_instruction($OPcode, $arg1 = null, $arg2 = null, $arg3 = null) {
      if ($this->writer == null) {
        fwrite($this->errf, ERR_NOTINIT);
        exit (99);
      }

      $this->writer->startElement('instruction');

      $this->writer->startAttribute('order');
      $this->writer->text($this->instruction_count);
      $this->writer->endAttribute();
      $this->instruction_count++;

      $this->writer->startAttribute('opcode');
      $this->writer->text($OPcode);
      $this->writer->endAttribute();

      if ($this->write_argument($arg1, 'arg1') == 1) {
        $this->writer->endElement();
        return;
      } elseif ($this->write_argument($arg2, 'arg2') == 1) {
        $this->writer->endElement();
        return;
      } elseif ($this->write_argument($arg3, 'arg3') == 1) {
        $this->writer->endElement();
        return;
      } else {
        $this->writer->endElement();
        return;
      }
    }

    /// Writes a single argument XML element.
    /// @param $arg       String containing IPPcode19 representation of the argument.
    /// @param $arg_name  String containing either 'arg1', 'arg2' or 'arg3',
    ///                   depending on the number of the argument
    /// @return           1 - if $arg is null.
    ///                   0 - if $arg is successfully used.
    private function write_argument($arg, $arg_name) {
      if ($this->writer == null) {
        fwrite($this->errf, ERR_NOTINIT);
        exit(99);
      }

      if ($arg == null)
        return 1;
      elseif (preg_match_all('/.*#.*/', $arg))
        exit(99);
      else {
        $arg_attributes = preg_split(self::R_ARGSPLIT, $arg);
        $this->writer->startElement($arg_name);
        $this->writer->startAttribute('type');

        if(count($arg_attributes) == 1) {
          $this->writer->text('label');
          $this->writer->endAttribute();
          $this->writer->text($arg_attributes[0]);
          $this->writer->endElement();
          return 0;
        } else {
          if (preg_match_all(self::R_VAR, $arg))
            $this->writer->text('var');
          else
            $this->writer->text($arg_attributes[0]);

          $this->writer->endAttribute();
          $this->writer->text($arg_attributes[1]);
          $this->writer->endElement();
          return 0;
        }
      }
    }

    /// Ends 'program' element and ends the XML document, finalizing it.
    /// Has to be called before print()
    function finalize() {
      if ($this->writer == null) {
        fwrite($this->errf, ERR_NOTINIT);
        exit(99);
      }

      $this->writer->endElement();
      $this->writer->endDocument();
    }

    /// Prints out contents of XML writer memory on STDOUT.
    /// finalize() has to be called before calling print()
    function print() {
      if ($this->writer == null) {
        fwrite($this->errf, ERR_NOTINIT);
        exit(99);
      }

      $this->writer->flush();
    }
  }

  #####################
  ## START OF SCRIPT ##
  #####################

// program setup
  $lex = new Lexical_Analyzer();

  $args = getopt('i:');

  $stdin = null;

  if ($args == false) {
      $stdin = fopen('php://stdin', 'r');
  } else {
      $stdin = fopen($args['i'], 'r');
  }

  if ($stdin == false)
    exit(11);

  $lex->check_for_header($stdin);

  $xmlm = new XML_Manager();
  $xmlm->init();

// program main
  $lex->main_loop($stdin, $xmlm);

// program output
  $xmlm->finalize();
  //$xmlm->print();

// stalling the program after finish
  fgets($stdin);
?>
