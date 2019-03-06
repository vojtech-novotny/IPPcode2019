<?php
  class lexicalAnalyzator {
    # MOVE〈var〉〈symb〉
    const R_MOVE = '/^\s*MOVE[\t\f ]+[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*|bool@(true|false)|nil@nil)\s*/';
    # CREATEFRAME
    const R_CREATEFRAME = '/^\s*CREATEFRAME.*/';
    # PUSHFRAME
    const R_PUSHFRAME = '/^\s*PUSHFRAME.*/';
    # POPFRAME
    const R_POPFRAME = '/^\s*POPFRAME.*/';
    # DEFVAR〈var〉
    const R_DEFVAR = '/^\s*DEFVAR[\t\f ]+[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*\s*/';
    # CALL〈label〉
    const R_CALL = '/^\s*CALL[\t\f ]+[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*\s*/';
    # RETURN
    const R_RETURN = '/^\s*RETURN.*/';
    # PUSHS〈symb〉
    const R_PUSHS = '/^\s*PUSHS[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*|bool@(true|false)|nil@nil)\s*/';
    # POPS〈var〉
    const R_POPS = '/^\s*POPS[\t\f ]+[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*\s*/';
    # ADD〈var〉〈symb1〉〈symb2〉
    const R_ADD = '/^\s*ADD[\t\f ]+[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)\s*/';
    # SUB〈var〉〈symb1〉〈symb2〉
    const R_SUB = '/^\s*SUB[\t\f ]+[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)\s*/';
    # MUL〈var〉〈symb1〉〈symb2〉
    const R_MUL = '/^\s*MUL[\t\f ]+[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)\s*/';
    # IDIV〈var〉〈symb1〉〈symb2〉
    const R_IDIV = '/^\s*IDIV[\t\f ]+[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)[\t\f ]+([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*)\s*/';
    # LT〈var〉〈symb1〉〈symb2〉
    const R_LT = '/^\s*LT.*/';
    # GT〈var〉〈symb1〉〈symb2〉
    const R_GT = '/^\s*GT.*/';
    # EQ〈var〉〈symb1〉〈symb2〉
    const R_EQ = '/^\s*EQ.*/';
    const R_AND = '/^\s*AND.*/';                    # AND〈var〉〈symb1〉〈symb2〉
    const R_OR = '/^\s*OR.*/';                      # OR〈var〉〈symb1〉〈symb2〉
    const R_NOT = '/^\s*NOT.*/';                    # NOT〈var〉〈symb1〉〈symb2〉
    const R_INT2CHAR = '/^\s*INT2CHAR.*/';          # INT2CHAR〈var〉〈symb〉
    const R_STRI2INT = '/^\s*STRI2INT.*/';          # STRI2INT〈var〉〈symb1〉〈symb2〉
    const R_READ = '/^\s*READ.*/';                  # READ〈var〉〈type〉
    const R_WRITE = '/^\s*WRITE.*/';                # WRITE〈symb〉
    const R_CONCAT = '/^\s*CONCAT.*/';              # CONCAT〈var〉〈symb1〉〈symb2〉
    const R_STRLEN = '/^\s*STRLEN.*/';              # STRLEN〈var〉〈symb〉
    const R_GETCHAR = '/^\s*GETCHAR.*/';            # GETCHAR〈var〉〈symb1〉〈symb2〉
    const R_SETCHAR = '/^\s*SETCHAR.*/';            # SETCHAR〈var〉〈symb1〉〈symb2〉
    const R_TYPE = '/^\s*TYPE.*/';                  # TYPE〈var〉〈symb〉
    const R_LABEL = '/^\s*LABEL.*/';                # LABEL〈label〉
    const R_JUMP = '/^\s*JUMP.*/';                  # JUMP〈label〉
    const R_JUMPIFEQ = '/^\s*JUMPIFEQ.*/';          # JUMPIFEQ〈label〉〈symb1〉〈symb2〉
    const R_JUMPIFNEQ = '/^\s*JUMPIFNEQ.*/';        # JUMPIFNEQ〈label〉〈symb1〉〈symb2〉
    const R_EXIT = '/^\s*EXIT.*/';                  # EXIT〈symb〉
    const R_DPRINT = '/^\s*DPRINT.*/';              # DPRINT〈symb〉
    const R_BREAK = '/^\s*BREAK.*/';                # BREAK

    const R_SPLIT = '/[\t\f ]+/';

    #const R_WHITESPACE = '/[\t\f ]+/'
    #const R_VAR = '/[GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*/'
    #const R_SYMB = ([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*|bool@(true|false)|nil@nil);
    #const R_LABEL;
    #const R_SYMB_INT = ([GLT]F@[a-zA-Z\_\-\$\&\%\*\?\!][a-zA-Z0-9\_\-\$\&\%\*\?\!]*|int@(\+|-)?[1-9][0-9]*);

    function analyze_instruction($line, $xmlW) {
      if (preg_match_all(self::R_MOVE, $line, $matches, PREG_SET_ORDER, 0 )) {
        $splitarray = preg_split(self::R_SPLIT, $line);
  			echo("FOUND MOVE\n");
  		} elseif (preg_match_all(self::R_CREATEFRAME, $line, $matches, PREG_SET_ORDER, 0 )) {
        echo("FOUND CREATEFRAME\n");
      } elseif (preg_match_all(self::R_PUSHFRAME, $line, $matches, PREG_SET_ORDER, 0 )) {
        echo("FOUND PUSHFRAME\n");
      } elseif (preg_match_all(self::R_POPFRAME, $line, $matches, PREG_SET_ORDER, 0 )) {
        echo("FOUND DEFVAR\n");
      } elseif (preg_match_all(self::R_DEFVAR, $line, $matches, PREG_SET_ORDER, 0 )) {

      }
    }

    function check_var($line) {

    }

    function check_symb($line) {

    }

    function check_label($line) {

    }

    function check_symb_int($line) {

    }

    function do_the_thing($stdin) {
      $i = 0;

      while ($i < 10) {
        #echo $i;
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
