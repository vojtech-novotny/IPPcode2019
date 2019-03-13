<?php
  class Lexical_Analyzer {

    // ### Definitions of regular expression detecting instruction OP Codes.

    /// @var Defines regex for matching empty lines (or with comments).
    const R_EMPTY = '/^[\t\f ]*(\#.*)?$/';

    /// @var Defines regex for detecting instruction # MOVE〈var〉〈symb〉
    const R_MOVE = '/^\s*MOVE.*/i';

    /// @var Defines regex for detecting instruction # CREATEFRAME
    const R_CREATEFRAME = '/^\s*CREATEFRAME.*/i';

    /// @var Defines regex for detecting instruction # PUSHFRAME
    const R_PUSHFRAME = '/^\s*PUSHFRAME.*/i';

    /// @var Defines regex for detecting instruction # POPFRAME
    const R_POPFRAME = '/^\s*POPFRAME.*/i';

    /// @var Defines regex for detecting instruction # DEFVAR〈var〉
    const R_DEFVAR = '/^\s*DEFVAR.*/i';

    /// @var Defines regex for detecting instruction # CALL〈label〉
    const R_CALL = '/^\s*CALL.*/i';

    /// @var Defines regex for detecting instruction # RETURN
    const R_RETURN = '/^\s*RETURN.*/i';

    /// @var Defines regex for detecting instruction # PUSHS〈symb〉
    const R_PUSHS = '/^\s*PUSHS.*/i';

    /// @var Defines regex for detecting instruction # POPS〈var〉
    const R_POPS = '/^\s*POPS.*/i';

    /// @var Defines regex for detecting instruction # ADD〈var〉〈symb1〉〈symb2〉
    const R_ADD = '/^\s*ADD.*/i';

    /// @var Defines regex for detecting instruction # SUB〈var〉〈symb1〉〈symb2〉
    const R_SUB = '/^\s*SUB.*/i';

    /// @var Defines regex for detecting instruction # MUL〈var〉〈symb1〉〈symb2〉
    const R_MUL = '/^\s*MUL.*/i';

    /// @var Defines regex for detecting instruction # IDIV〈var〉〈symb1〉〈symb2〉
    const R_IDIV = '/^\s*IDIV.*/i';

    /// @var Defines regex for detecting instruction # LT〈var〉〈symb1〉〈symb2〉
    const R_LT = '/^\s*LT.*/i';

    /// @var Defines regex for detecting instruction # GT〈var〉〈symb1〉〈symb2〉
    const R_GT = '/^\s*GT.*/i';

    /// @var Defines regex for detecting instruction # EQ〈var〉〈symb1〉〈symb2〉
    const R_EQ = '/^\s*EQ.*/i';

    /// @var Defines regex for detecting instruction # AND〈var〉〈symb1〉〈symb2〉
    const R_AND = '/^\s*AND.*/i';

    /// @var Defines regex for detecting instruction # OR〈var〉〈symb1〉〈symb2〉
    const R_OR = '/^\s*OR.*/i';

    /// @var Defines regex for detecting instruction # NOT〈var〉〈symb1〉〈symb2〉
    const R_NOT = '/^\s*NOT.*/i';

    /// @var Defines regex for detecting instruction # INT2CHAR〈var〉〈symb〉
    const R_INT2CHAR = '/^\s*INT2CHAR.*/i';

    /// @var Defines regex for detecting instruction # STRI2INT〈var〉〈symb1〉〈symb2〉
    const R_STRI2INT = '/^\s*STRI2INT.*/i';

    /// @var Defines regex for detecting instruction # READ〈var〉〈type〉
    const R_READ = '/^\s*READ.*/i';

    /// @var Defines regex for detecting instruction # WRITE〈symb〉
    const R_WRITE = '/^\s*WRITE.*/i';

    /// @var Defines regex for detecting instruction # CONCAT〈var〉〈symb1〉〈symb2〉
    const R_CONCAT = '/^\s*CONCAT.*/i';

    /// @var Defines regex for detecting instruction # STRLEN〈var〉〈symb〉
    const R_STRLEN = '/^\s*STRLEN.*/i';

    /// @var Defines regex for detecting instruction # GETCHAR〈var〉〈symb1〉〈symb2〉
    const R_GETCHAR = '/^\s*GETCHAR.*/i';

    /// @var Defines regex for detecting instruction # SETCHAR〈var〉〈symb1〉〈symb2〉
    const R_SETCHAR = '/^\s*SETCHAR.*/i';

    /// @var Defines regex for detecting instruction # TYPE〈var〉〈symb〉
    const R_TYPE = '/^\s*TYPE.*/i';

    /// @var Defines regex for detecting instruction # LABEL〈label〉
    const R_LABEL = '/^\s*LABEL.*/i';

    /// @var Defines regex for detecting instruction # JUMP〈label〉
    const R_JUMP = '/^\s*JUMP.*/i';

    /// @var Defines regex for detecting instruction # JUMPIFEQ〈label〉〈symb1〉〈symb2〉
    const R_JUMPIFEQ = '/^\s*JUMPIFEQ.*/i';

    /// @var Defines regex for detecting instruction # JUMPIFNEQ〈label〉〈symb1〉〈symb2〉
    const R_JUMPIFNEQ = '/^\s*JUMPIFNEQ.*/i';

    /// @var Defines regex for detecting instruction # EXIT〈symb〉
    const R_EXIT = '/^\s*EXIT.*/i';

    /// @var Defines regex for detecting instruction # DPRINT〈symb〉
    const R_DPRINT = '/^\s*DPRINT.*/i';

    /// @var Defines regex for detecting instruction # BREAK
    const R_BREAK = '/^\s*BREAK.*/i';


    // ### Definitions of regular expressions detecting instruction operands.

    /// @var Defines regex for matching whitespace between instructions and operands. Doesn't match newlines.
    const R_WHITESPACE = '/[\t\f ]+/';

    /// @var Defines regex for matching variable operands.
    const R_VAR = '/[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*/';

    /// @var Defines regex for matching variable or literal operands.
    const R_SYMB = '/([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*|bool@(true|false)|string@([^\\\s#]|\\[0-9][0-9][0-9])*|nil@nil)/';

    /// @var Defines regex for matching variable or integer literal operands.
    const R_SYMB_INT = '/([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)/';

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
      } elseif (preg_match_all(self::R_CREATEFRAME, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('CREATEFRAME');
        else exit(23);
      } elseif (preg_match_all(self::R_PUSHFRAME, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('PUSHFRAME');
        else exit(23);
      } elseif (preg_match_all(self::R_POPFRAME, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('POPFRAME');
        else exit(23);
      } elseif (preg_match_all(self::R_DEFVAR, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('DEFVAR', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_CALL, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('CALL', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_RETURN, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   $xmlm->write_instruction('RETURN');
        else exit(23);
      } elseif (preg_match_all(self::R_PUSHS, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('PUSHS', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_POPS, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('POPS', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_ADD, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('ADD', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_SUB, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('SUB', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_MUL, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('MUL', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_IDIV, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('IDIV', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_LT, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('LT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_GT, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('GT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_EQ, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('EQ', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_AND, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('AND', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_OR, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('OR', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_NOT, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('NOT', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_INT2CHAR, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('INT2CHAR', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_STRI2INT, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('STRI2INT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_TYPE, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_TYPEARG, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('TYPE', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_WRITE, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('WRITE', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_CONCAT, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && preg_match_all(self::R_SYMB_STRING, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('CONCAT', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_STRLEN, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('STRLEN', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_GETCHAR, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_STRING, $tokens[2]) && preg_match_all(self::R_SYMB_INT, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('GETCHAR', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_SETCHAR, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB_INT, $tokens[2]) && preg_match_all(self::R_SYMB_STRING, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('SETCHAR', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_TYPE, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   $xmlm->write_instruction('TYPE', $tokens[1], $tokens[2]);
        else exit(23);
      } elseif (preg_match_all(self::R_LABEL, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('LABEL', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_JUMP, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('JUMP', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_JUMPIFEQ, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('JUMPIFEQ', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_JUMPIFNEQ, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && preg_match_all(self::R_SYMB, $tokens[3]) && ($token_count == 4 || preg_match_all(self::R_COMMENT, $tokens[4])))
  			   $xmlm->write_instruction('JUMPIFNEQ', $tokens[1], $tokens[2], $tokens[3]);
        else exit(23);
      } elseif (preg_match_all(self::R_EXIT, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('EXIT', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_DPRINT, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   $xmlm->write_instruction('DPRINT', $tokens[1]);
        else exit(23);
      } elseif (preg_match_all(self::R_BREAK, $line, $matches, PREG_SET_ORDER, 0)) {
        if (($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1])))
  			   $xmlm->write_instruction('BREAK');
        else exit(23);
      } else exit(22);
    }

    function do_the_thing($stdin, $xmlm) {
      $i = 0;

      while ($i < 15) {
        echo $i . ' ';
        $line = trim(fgets($stdin));
        self::analyze_instruction($line, $xmlm);
        $i = $i + 1;
      }
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
        $this->writer->startDocument();
        $this->writer->startElement('program');
        $this->writer->startAttribute('language');
        $this->writer->text('IPPcode19');
        $this->writer->endAttribute();
      } else {
        fwrite($this->errf, ERR_REINIT);
        exit (-1);
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
        exit (-1);
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
        exit(-1);
      }

      if ($arg == null)
        return 1;
      else {
        $arg_attributes = preg_split(self::R_ARGSPLIT, $arg);
        $this->writer->startElement($arg_name);
        $this->writer->startAttribute('type');

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

    /// Ends 'program' element and ends the XML document, finalizing it.
    /// Has to be called before print()
    function finalize() {
      if ($this->writer == null) {
        fwrite($this->errf, ERR_NOTINIT);
        exit(-1);
      }

      $this->writer->endElement();
      $this->writer->endDocument();
    }

    /// Prints out contents of XML writer memory on STDOUT.
    /// finalize() has to be called before calling print()
    function print() {
      if ($this->writer == null) {
        fwrite($this->errf, ERR_NOTINIT);
        exit(-1);
      }

      $this->writer->flush();
    }
  }

  #####################
  ## START OF SCRIPT ##
  #####################

// program setup
  $lex = new Lexical_Analyzer();
  $stdin = fopen('php://stdin', 'r');
  $xmlm = new XML_Manager();
  $xmlm->init();

// program main
  $lex->do_the_thing($stdin, $xmlm);

// program output
  $xmlm->finalize();
  $xmlm->print();

// stalling the program after finish
  fgets($stdin);
?>
