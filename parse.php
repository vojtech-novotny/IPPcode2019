<?php
  class lexicalAnalyzator {

    // ### Definitions of regular expression detecting instruction OP Codes.

    /// @var Defines regex for detecting instruction # MOVE〈var〉〈symb〉
    const R_MOVE = '/^\s*MOVE.*/';

    /// @var Defines regex for detecting instruction # CREATEFRAME
    const R_CREATEFRAME = '/^\s*CREATEFRAME.*/';

    /// @var Defines regex for detecting instruction # PUSHFRAME
    const R_PUSHFRAME = '/^\s*PUSHFRAME.*/';

    /// @var Defines regex for detecting instruction # POPFRAME
    const R_POPFRAME = '/^\s*POPFRAME.*/';

    /// @var Defines regex for detecting instruction # DEFVAR〈var〉
    const R_DEFVAR = '/^\s*DEFVAR.*/';

    /// @var Defines regex for detecting instruction # CALL〈label〉
    const R_CALL = '/^\s*CALL.*/';

    /// @var Defines regex for detecting instruction # RETURN
    const R_RETURN = '/^\s*RETURN.*/';

    /// @var Defines regex for detecting instruction # PUSHS〈symb〉
    const R_PUSHS = '/^\s*PUSHS.*/';

    /// @var Defines regex for detecting instruction # POPS〈var〉
    const R_POPS = '/^\s*POPS.*/';

    /// @var Defines regex for detecting instruction # ADD〈var〉〈symb1〉〈symb2〉
    const R_ADD = '/^\s*ADD.*/';

    /// @var Defines regex for detecting instruction # SUB〈var〉〈symb1〉〈symb2〉
    const R_SUB = '/^\s*SUB.*/';

    /// @var Defines regex for detecting instruction # MUL〈var〉〈symb1〉〈symb2〉
    const R_MUL = '/^\s*MUL.*/';

    /// @var Defines regex for detecting instruction # IDIV〈var〉〈symb1〉〈symb2〉
    const R_IDIV = '/^\s*IDIV.*/';

    /// @var Defines regex for detecting instruction # LT〈var〉〈symb1〉〈symb2〉
    const R_LT = '/^\s*LT.*/';

    /// @var Defines regex for detecting instruction # GT〈var〉〈symb1〉〈symb2〉
    const R_GT = '/^\s*GT.*/';

    /// @var Defines regex for detecting instruction # EQ〈var〉〈symb1〉〈symb2〉
    const R_EQ = '/^\s*EQ.*/';

    /// @var Defines regex for detecting instruction # AND〈var〉〈symb1〉〈symb2〉
    const R_AND = '/^\s*AND.*/';

    /// @var Defines regex for detecting instruction # OR〈var〉〈symb1〉〈symb2〉
    const R_OR = '/^\s*OR.*/';

    /// @var Defines regex for detecting instruction # NOT〈var〉〈symb1〉〈symb2〉
    const R_NOT = '/^\s*NOT.*/';

    /// @var Defines regex for detecting instruction # INT2CHAR〈var〉〈symb〉
    const R_INT2CHAR = '/^\s*INT2CHAR.*/';

    /// @var Defines regex for detecting instruction # STRI2INT〈var〉〈symb1〉〈symb2〉
    const R_STRI2INT = '/^\s*STRI2INT.*/';

    /// @var Defines regex for detecting instruction # READ〈var〉〈type〉
    const R_READ = '/^\s*READ.*/';

    /// @var Defines regex for detecting instruction # WRITE〈symb〉
    const R_WRITE = '/^\s*WRITE.*/';

    /// @var Defines regex for detecting instruction # CONCAT〈var〉〈symb1〉〈symb2〉
    const R_CONCAT = '/^\s*CONCAT.*/';

    /// @var Defines regex for detecting instruction # STRLEN〈var〉〈symb〉
    const R_STRLEN = '/^\s*STRLEN.*/';

    /// @var Defines regex for detecting instruction # GETCHAR〈var〉〈symb1〉〈symb2〉
    const R_GETCHAR = '/^\s*GETCHAR.*/';

    /// @var Defines regex for detecting instruction # SETCHAR〈var〉〈symb1〉〈symb2〉
    const R_SETCHAR = '/^\s*SETCHAR.*/';

    /// @var Defines regex for detecting instruction # TYPE〈var〉〈symb〉
    const R_TYPE = '/^\s*TYPE.*/';

    /// @var Defines regex for detecting instruction # LABEL〈label〉
    const R_LABEL = '/^\s*LABEL.*/';

    /// @var Defines regex for detecting instruction # JUMP〈label〉
    const R_JUMP = '/^\s*JUMP.*/';

    /// @var Defines regex for detecting instruction # JUMPIFEQ〈label〉〈symb1〉〈symb2〉
    const R_JUMPIFEQ = '/^\s*JUMPIFEQ.*/';

    /// @var Defines regex for detecting instruction # JUMPIFNEQ〈label〉〈symb1〉〈symb2〉
    const R_JUMPIFNEQ = '/^\s*JUMPIFNEQ.*/';

    /// @var Defines regex for detecting instruction # EXIT〈symb〉
    const R_EXIT = '/^\s*EXIT.*/';

    /// @var Defines regex for detecting instruction # DPRINT〈symb〉
    const R_DPRINT = '/^\s*DPRINT.*/';

    /// @var Defines regex for detecting instruction # BREAK
    const R_BREAK = '/^\s*BREAK.*/';


    // ### Definitions of regular expressions detecting instruction operands.

    /// @var Defines regex for matching whitespace between instructions and operands. Doesn't match newlines.
    const R_WHITESPACE = '/[\t\f ]+/';

    /// @var Defines regex for matching variable operands.
    const R_VAR = '/[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*/';

    /// @var Defines regex for matching variable or literal operands.
    const R_SYMB = '/([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*|bool@(true|false)|string@([^\\\s#]|\\[0-9][0-9][0-9])*|nil@nil)/';

    /// @var Defines regex for matching variable or integer literal operands.
    const R_SYMB_INT = '/([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)/';

    /// @var Defines regex for matching label operands.
    const R_LABELARG = '/[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*\s*/';

    /// @var Defines regex for matching comments.
    const R_COMMENT = '/\#.*/';


    /// Analyzes a line of code detecting instructions and their operands,
    /// checks whether they are syntacticly correct.
    /// Syntacticaly correct constructions writes into an xml document.
    /// @param $line  The line of code being analyzed.
    /// @param $xmlW  The XML Writer object used to write the xml document.
    function analyze_instruction($line, $xmlW) {
      // Splits the line into an array of tokens.
      $tokens = preg_split(self::R_WHITESPACE, $line);
      $token_count = count($token_count);

      // Analyzes the line itself and subsequently all the tokens.
      if (preg_match_all(self::R_MOVE, $line)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && preg_match_all(self::R_SYMB, $tokens[2]) && ($token_count == 3 || preg_match_all(self::R_COMMENT, $tokens[3])))
  			   echo("FOUND MOVE\n");
        else exit(22);
      } elseif (preg_match_all(self::R_CREATEFRAME, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   echo("FOUND CREATEFRAME\n");
        else exit(22);
      } elseif (preg_match_all(self::R_PUSHFRAME, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   echo("FOUND PUSHFRAME\n");
        else exit(22);
      } elseif (preg_match_all(self::R_POPFRAME, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   echo("FOUND POPFRAME\n");
        else exit(22);
      } elseif (preg_match_all(self::R_DEFVAR, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   echo("FOUND MOVE\n");
        else exit(22);
      } elseif (preg_match_all(self::R_CALL, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_LABELARG, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   echo("FOUND CALL\n");
        else exit(22);
      } elseif (preg_match_all(self::R_RETURN, $line, $matches, PREG_SET_ORDER, 0)) {
        if ($token_count == 1 || preg_match_all(self::R_COMMENT, $tokens[1]))
  			   echo("FOUND RETURN\n");
        else exit(22);
      } elseif (preg_match_all(self::R_PUSHS, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_SYMB, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   echo("FOUND MOVE\n");
        else exit(22);
      } elseif (preg_match_all(self::R_POPS, $line, $matches, PREG_SET_ORDER, 0)) {
        if (preg_match_all(self::R_VAR, $tokens[1]) && ($token_count == 2 || preg_match_all(self::R_COMMENT, $tokens[2])))
  			   echo("FOUND MOVE\n");
        else exit(22);
      }
    }

    function do_the_thing($stdin) {
      $i = 0;

      while ($i < 15) {
        echo $i;
        $line = fgets($stdin);
        self::analyze_instruction($line, $xmlW);
        $i = $i + 1;
      }
    }
  }

  #####################
  ## START OF SCRIPT ##
  #####################

  $lex = new lexicalAnalyzator();
  $stdin = fopen('php://stdin', 'r');
  $xmlW = new XMLWriter();
  $xmlW->openMemory();
  $xmlW->setIndent(1);
  $xmlW->startDocument();
  $xmlW->startElement("Program");

  $lex->do_the_thing($stdin);
  #$lex->echolines();

  $xmlW->endElement();
  $xmlW->endDocument();
  #echo $xmlW->outputMemory();
  fclose($stdin);
?>
