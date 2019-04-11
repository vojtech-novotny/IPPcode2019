import xml.etree.ElementTree as ET
import sys
import argparse

# reading command line arguments
arg_parser = argparse.ArgumentParser(description='Program loads XML representation of IPPcode19 program from input and interprets it.')
arg_parser.add_argument('-s','--source', action='store', dest='source', help="Path to the file containing input for the program. If parameter is missing, stdin is used. SOURCE and INPUT can't be identical paths (and one of them always has to be defined).")
arg_parser.add_argument('-i','--input', action='store', dest='input', help="Path to the file containing source for the program. If parameter is missing, stdin is used. SOURCE and INPUT can't be identical paths (and one of them always has to be defined).")
args = arg_parser.parse_args()

# file containing XML representation of IPPcode19 source code
source_path = None
# file containing text for READ instructions
input_path = None
input_file = None

# unused - help is generated using arg_parser functions.
help = """
Program loads XML representation of IPPcode19 program from input and interprets it.

Program parameters:
 -h, --help
    ... Prints out this help message and terminates.

 -i <ipath>, --input <ipath>
    ... Path to the file containing input for the program.
        If parameter is missing, stdin is used.

 -s <spath>, --source <spath>
    ... Path to the file containing source for the program.
        If parameter is missing, stdin is used.

 SOURCE and INPUT can't be identical paths (and one of them always
 has to be defined).
"""

if args.source != None:
    source_path = args.source
else:
    source_path = sys.stdin

if args.input != None:
    input_path = args.input
    input_file = open(input_path, 'r')
else:
    input_path = sys.stdin
    input_file = sys.stdin

if input_path == source_path:
    sys.exit(10)




class Parser:
    """Main program controller, parses instructions out of an XML file.
       XML file is found at the source_path path."""

    IP = 1
    """Instruction pointer."""

    tree = ET.parse(source_path)
    root = tree.getroot()

    def foo(self, interpret, symtable):
        """Reads instructions from input checks with the instruction table whether they are valid
           and executes them.

           Arguments:
            interpret   - class containing functions that execute corresponding instructions
            symtable    - table of symbols, containing all the state information about
                          the IPPcode19 program (except for the instruction pointer)"""

        # the instruction table
        instructions = {
            "MOVE":interpret.do_MOVE,
            "DEFVAR":interpret.do_DEFVAR,
            "ADD":interpret.do_Arithmetic,
            "SUB":interpret.do_Arithmetic,
            "MUL":interpret.do_Arithmetic,
            "IDIV":interpret.do_Arithmetic,
            "WRITE":interpret.do_WRITE,
            "LT":interpret.do_Comparison,
            "GT":interpret.do_Comparison,
            "EQ":interpret.do_Comparison,
            "AND":interpret.do_Logic,
            "OR":interpret.do_Logic,
            "NOT":interpret.do_NOT,
            "LABEL":interpret.do_LABEL,
            "JUMP":interpret.do_JUMP,
            "JUMPIFEQ":interpret.do_JumpLogic,
            "JUMPIFNEQ":interpret.do_JumpLogic,
            "READ":interpret.do_READ,
            "STRLEN":interpret.do_STRLEN,
            "CONCAT":interpret.do_CONCAT,
            "GETCHAR":interpret.do_GETCHAR,
            "SETCHAR":interpret.do_SETCHAR,
            "INT2CHAR":interpret.do_INT2CHAR,
            "STRI2INT":interpret.do_STRI2INT,
            "EXIT":interpret.do_STRI2INT,
            "TYPE":interpret.do_TYPE,
        }

        # predefining labels
        self.interpret_all_labels(symtable)

        instruction_found = True

        while (instruction_found):
            instruction = self.get_instruction(self.IP)

            # if the next instruction isn't found, program terminates.
            if (instruction == None):
                instruction_found = False
                continue

            # if opcode is not found in the instruction table, program terminates with exit code 32.
            opcode = instruction.attrib['opcode']
            if opcode not in instructions:
                sys.exit(32)

            result = instructions[opcode](instruction, symtable)

            self.change_ip(result)


    def get_instruction(self, ip):
        """Tries to find the instruction with the order == ip.
        Returns the instruction if it's found and None otherwise."""

        for instruction in self.root.findall('instruction'):
            if instruction.attrib['order'] == str(ip):
                return instruction
        return None

    def change_ip(self, result):
        """Increments the instruction pointer or changes the value
        after one of the JUMP instructions."""

        if result == True or result == False:
            self.IP += 1
        else:
            self.IP = result

    def interpret_all_labels(self, symtable):
        """Finds all the label instructions and inserts them into the symtable,
        so they can be accessed by instructions before their code definition."""

        for instruction in self.root.findall('instruction'):
            if instruction.attrib['opcode'] == 'LABEL':
                symtable.define_label(instruction[0].text, int(instruction.attrib['order']))



class Interpret:
    """Interprets(executes) code parsed out of XML by Parser."""

    """Public functions have these arguments:
        instruction - xml object containing information about the instructions
        symtable    - table of symbols, containing all the state information about
                      the IPPcode19 program (except for instruction pointer)

       Some functions also use these:
        name        - name of the destination variable
        valueN      - value of an operand
        typeN       - type of an operand
        - Functions that use these are 'private', they are not called from the Parser
          but rather from other functions inside the Interpret. This is done to reuse
          code in instances where there are multiple instructions with the same operand
          semantics but different operators."""

    def do_DEFVAR(self, instruction, symtable):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        return symtable.define_var(instruction[0].text)

    def do_MOVE(self, instruction, symtable):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type = instruction[1].attrib['type']
        value = None

        if (type == 'var'):
            type = symtable.get_var(instruction[1].text)
            value = type[1]
            type = type[0]
        elif type == 'int':
            value = int(instruction[1].text)
        elif type == 'bool':
            value = instruction[1].text == 'true'
        elif type == 'string':
            value = instruction[1].text
        elif type == 'nil':
            value = None
        else:
            sys.exit(53)

        return symtable.set_var(instruction[0].text, type, value)

    def do_EXIT(self, instruction, symtable):         #, symb):
        type = instruction[0].attrib['type']
        value = None

        if (type == 'var'):
            value = symtable.get_var(instruction[0].text)
            type = value[0]
            if (type != 'int'):
                sys.exit(53)
            value = value[1]
        elif type == 'int':
            value = int(instruction[0].text)
        else:
            sys.exit(53)

        if value < 0 or value > 49:
            sys.exit(57)

        sys.exit(value)

    def do_TYPE(self, instruction, symtable):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type1 = instruction[1].attrib['type']

        if (type1 == 'var'):
            type1 = symtable.get_var(instruction[1].text)[0]

        return symtable.set_var(instruction[0].text, "string", "" if type1 == None else type1)

    # IO instructions
    def do_WRITE(self, instruction, symtable):
        result = None

        if (instruction[0].attrib['type'] == 'var'):
            result = str(symtable.get_var(instruction[0].text)[1])
        else:
            result = instruction[0].text

        print(result)
        return True

    def do_READ(self, instruction, symtable):         #, var, symb1, symb2):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        input = input_file.readline()
        # getting rid of trailing newlines (readline() artefact)
        input = input.rstrip('\n')

        type = instruction[1].attrib['type']
        value = instruction[1].text;

        if type == 'type':
            if value == 'int':
                try:
                    input = int(input)
                except:
                    input = 0
                return symtable.set_var(instruction[0].text, 'int', int(input))
            elif value == 'bool':
                return symtable.set_var(instruction[0].text, 'bool', input.lower() == 'true')
            elif value == 'string':
                return symtable.set_var(instruction[0].text, 'int', input)
            else:
                sys.exit(53)
        else:
            sys.exit(53)

    # arithmetic instructions
    def do_Arithmetic(self, instruction, symtable):
        """All the typechecking and defchecking is done in one function,
        arithmetic computation is done in separate functions at the end."""

        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            if (type1 != 'int'):
                sys.exit(53)
            value1 = value1[1]
        elif type1 == 'int':
            value1 = int(instruction[1].text)
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            if (type2 != 'int'):
                sys.exit(53)
            value2 = value2[1]
        elif type2 == 'int':
            value2 = int(instruction[2].text)
        else:
            sys.exit(53)

        # Actual computation:
        if instruction.attrib['opcode'] == 'ADD':
            return self.do_ADD(symtable, instruction[0].text, value1, value2, type1)
        elif instruction.attrib['opcode'] == 'SUB':
            return self.do_SUB(symtable, instruction[0].text, value1, value2, type1)
        elif instruction.attrib['opcode'] == 'MUL':
            return self.do_MUL(symtable, instruction[0].text, value1, value2, type1)
        elif instruction.attrib['opcode'] == 'IDIV':
            if (value2 == 0):
                sys.exit(57)
            return self.do_IDIV(symtable, instruction[0].text, value1, value2, type1)
        else:
            sys.exit(32)

    def do_ADD(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 + value2)

    def do_SUB(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 - value2)

    def do_MUL(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 * value2)

    def do_IDIV(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 // value2)

    # comparison instructions
    def do_Comparison(self, instruction, symtable):
        """All the typechecking and defchecking is done in one function,
        comparison computation is done in separate functions at the end."""

        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            value1 = value1[1]
        elif type1 == 'int':
            value1 = int(instruction[1].text)
        elif type1 == 'bool':
            value1 = instruction[1].text == 'true'
        elif type1 == 'string':
            value1 = instruction[1].text
        elif type1 == 'nil':
            value1 = None
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            value2 = value2[1]
        elif type2 == 'int':
            value2 = int(instruction[2].text)
        elif type2 == 'bool':
            value2 = instruction[2].text == 'true'
        elif type2 == 'string':
            value2 = instruction[2].text
        elif type2 == 'nil':
            value2 = None
        else:
            sys.exit(53)

        if type1 != type2:
            # nil@nil can be compared with anything using the instruction EQ
            if type1 != 'nil' and type2 != 'nil' or instruction.attrib['opcode'] != 'EQ':
                sys.exit(53)

        # Actual computation:
        if instruction.attrib['opcode'] == 'EQ':
            return self.do_EQ(symtable, instruction[0].text, value1, value2, type1, type2)
        elif instruction.attrib['opcode'] == 'LT':
            return self.do_LT(symtable, instruction[0].text, value1, value2)
        elif instruction.attrib['opcode'] == 'GT':
            return self.do_GT(symtable, instruction[0].text, value1, value2)
        else:
            sys.exit(32)

    def do_LT(self, symtable, name, value1, value2):          #, var, symb1, symb2):
        return symtable.set_var(name, 'bool', value1 < value2)

    def do_GT(self, symtable, name, value1, value2):           #, var, symb1, symb2):
        return symtable.set_var(name, 'bool', value1 > value2)

    def do_EQ(self, symtable, name, value1, value2, type1, type2):           #, var, symb1, symb2):
        return symtable.set_var(name, 'bool', type1 == type2 and value1 == value2)

    # logic instructions
    def do_Logic(self, instruction, symtable):
        """All the typechecking and defchecking is done in one function,
        logic computation is done in separate functions at the end."""

        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            if (type1 != 'bool'):
                sys.exit(53)
            value1 = value1[1]
        elif type1 == 'bool':
            value1 = instruction[1].text == 'true'
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            if (type2 != 'bool'):
                sys.exit(53)
            value2 = value2[1]
        elif type2 == 'bool':
            value2 = instruction[2].text == 'true'
        else:
            sys.exit(53)

        # Actual computation:
        if instruction.attrib['opcode'] == 'AND':
            return self.do_AND(symtable, instruction[0].text, value1, value2)
        elif instruction.attrib['opcode'] == 'OR':
            return self.do_OR(symtable, instruction[0].text, value1, value2)
        else:
            sys.exit(32)

    def do_AND(self, symtable, name, value1, value2):          #, var, symb1, symb2):
        return symtable.set_var(name, 'bool', value1 and value2)

    def do_OR(self, symtable, name, value1, value2):            #, var, symb1, symb2):
        return symtable.set_var(name, 'bool', value1 or value2)

    def do_NOT(self, instruction, symtable):            #, var, symb1, symb2):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type = instruction[1].attrib['type']
        value = None

        if (type == 'var'):
            value = symtable.get_var(instruction[1].text)
            type = value[0]
            if (type != 'bool'):
                sys.exit(53)
            value = value[1]
        elif type == 'bool':
            value = instruction[1].text == 'true'
        else:
            sys.exit(53)

        return symtable.set_var(instruction[0].text, 'bool', not value)

    # flow control instructions
    def do_LABEL(self, instruction, symtable):        #, label):
        return True

    def do_JUMP(self, instruction, symtable):         #, label):
        # not a label
        if instruction[0].attrib['type'] != 'label':
            sys.exit(53)

        return symtable.get_label(instruction[0].text)

    def do_JumpLogic(self, instruction, symtable):
        """All the typechecking and defchecking is done in one function,
        comparison computation is done in separate functions at the end."""

        # not a label
        if instruction[0].attrib['type'] != 'label':
            sys.exit(53)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            value1 = value1[1]
        elif type1 == 'int':
            value1 = int(instruction[1].text)
        elif type1 == 'bool':
            value1 = instruction[1].text == 'true'
        elif type1 == 'string':
            value1 = instruction[1].text
        elif type1 == 'nil':
            value1 = None
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            value2 = value2[1]
        elif type2 == 'int':
            value2 = int(instruction[2].text)
        elif type2 == 'bool':
            value2 = instruction[2].text == 'true'
        elif type2 == 'string':
            value2 = instruction[2].text
        elif type2 == 'nil':
            value2 = None
        else:
            sys.exit(53)

        if type1 != type2:
            sys.exit(53)

        # Actual computation:
        if instruction.attrib['opcode'] == 'JUMPIFEQ':
            return self.do_JUMPIFEQ(symtable, instruction[0].text, value1, value2)
        elif instruction.attrib['opcode'] == 'JUMPIFNEQ':
            return self.do_JUMPIFNEQ(symtable, instruction[0].text, value1, value2)
        else:
            sys.exit(32)

    def do_JUMPIFEQ(self, symtable, name, value1, value2):
        if value1 == value2:
            return symtable.get_label(name)
        else:
            return False

    def do_JUMPIFNEQ(self, symtable, name, value1, value2):
        if value1 != value2:
            return symtable.get_label(name)
        else:
            return False

    # string manipulation instructions
    def do_STRLEN(self, instruction, symtable):       #, var, symb):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type = instruction[1].attrib['type']
        value = None

        if (type == 'var'):
            value = symtable.get_var(instruction[1].text)
            type = value[0]
            if (type != 'string'):
                sys.exit(53)
            value = value[1]
        elif type == 'string':
            value = instruction[1].text
        else:
            sys.exit(53)

        return symtable.set_var(instruction[0].text, 'int', len(value))

    def do_CONCAT(self, instruction, symtable):       #, var, symb1, symb2):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            if (type1 != 'string'):
                sys.exit(53)
            value1 = value1[1]
        elif type1 == 'string':
            value1 = instruction[1].text
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            if (type2 != 'string'):
                sys.exit(53)
            value2 = value2[1]
        elif type2 == 'string':
            value2 = instruction[2].text
        else:
            sys.exit(53)

        return symtable.set_var(instruction[0].text, 'string', value1 + value2)

    def do_GETCHAR(self, instruction, symtable):      #, var, symb1, symb2):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            if (type1 != 'string'):
                sys.exit(53)
            value1 = value1[1]
        elif type1 == 'string':
            value1 = instruction[1].text
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            if (type2 != 'int'):
                sys.exit(53)
            value2 = value2[1]
        elif type2 == 'int':
            value2 = int(instruction[2].text)
        else:
            sys.exit(53)

        if value2 >= len(value1):
            sys.exit(58)

        return symtable.set_var(instruction[0].text, 'string', value1[value2])

    def do_SETCHAR(self, instruction, symtable):      #, var, symb1, symb2):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        value0 = symtable.get_var(instruction[0].text)
        type0 = value0[0]
        if type0 != 'string':
            sys.exit(53)
        value0 = value0[1]

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            if (type1 != 'int'):
                sys.exit(53)
            value1 = value1[1]
        elif type1 == 'int':
            value1 = int(instruction[1].text)
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            if (type2 != 'string'):
                sys.exit(53)
            value2 = value2[1]
        elif type2 == 'string':
            value2 = instruction[2].text
        else:
            sys.exit(53)

        if value1 >= len(value0) or len(value2) == 0:
            sys.exit(58)

        value0_list = list(value0)
        value0_list[value1] = value2[0]
        value0 = ''.join(value0_list)

        return symtable.set_var(instruction[0].text, 'string', value0)

    def do_INT2CHAR(self, instruction, symtable):     #, var, symb):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type = instruction[1].attrib['type']
        value = None

        if (type == 'var'):
            value = symtable.get_var(instruction[1].text)
            type = value[0]
            if (type != 'int'):
                sys.exit(53)
            value = value[1]
        elif type == 'int':
            value = int(instruction[1].text)
        else:
            sys.exit(53)

        try:
            value = chr(value)
        except:
            sys.exit(58)

        return symtable.set_var(instruction[0].text, 'string', value)

    def do_STRI2INT(self, instruction, symtable):     #, var, symb1, symb2):
        # not a variable
        if instruction[0].attrib['type'] != 'var':
            sys.exit(53)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            if (type1 != 'string'):
                sys.exit(53)
            value1 = value1[1]
        elif type1 == 'string':
            value1 = instruction[1].text
        else:
            sys.exit(53)

        type2 = instruction[2].attrib['type']
        value2 = None

        if (type2 == 'var'):
            value2 = symtable.get_var(instruction[2].text)
            type2 = value2[0]
            if (type2 != 'int'):
                sys.exit(53)
            value2 = value2[1]
        elif type2 == 'int':
            value2 = int(instruction[2].text)
        else:
            sys.exit(53)

        if value2 >= len(value1):
            sys.exit(58)

        return symtable.set_var(instruction[0].text, 'int', ord(value1[value2]))



    # NOT IMPLEMENTED:

    # debug instructions
    def do_DPRINT(self, instruction):       #, symb):
        return True

    def do_BREAK(self, instruction):        #):
        return True

    # function instructions
    def do_CREATEFRAME(self, instruction):  #):
        return True

    def do_PUSHFRAME(self, instruction):    #):
        return True

    def do_POPFRAME(self, instruction):     #):
        return True

    def do_CALL(self, instruction):         #, label):
        return True

    def do_RETURN(self, instruction):       #):
        return True

    # stack instructions
    def do_PUSHS(self, instruction):        #, symb):
        return True

    def do_POPS(self, instruction):         #, var):
        return True



class Symtable:
    """Contains and manages all information about variables and labels.

       Warning: Function calls and frames are not implemented. All variables are global!"""

    # currently doesn't support frames
    var_table = {}      # { 'var_table':('type', 'value') }
    label_table = {}    # { 'label_table':'instruction_pointer' }

    # NOT IMPLEMENTED:
    LF_table = {}      # { 'var_table':('type', 'value') }
    GF_table = {}       # { 'var_table':('type', 'value') }
    TF_table = {}       # { 'var_table':('type', 'value') }

    def check_defined_var(self, name):
        return name in self.var_table

    def check_type(self, name):
        if name not in self.var_table:
            return None
        else:
            return self.var_table[name][0]

    def define_var(self, name):
        if name in self.var_table:
            sys.exit(52)
        else:
            self.var_table[name] = (None, None)
            return True

    def set_var(self, name, type, value):
        if name not in self.var_table:
            sys.exit(54)
        else:
            self.var_table[name] = (type, value)
            return True

    def get_var(self, name):
        if name not in self.var_table:
            sys.exit(54)
        else:
            return self.var_table[name]

    def check_defined_label(self, name):
        return name in self.label_table

    def define_label(self, name, IP):
        if name in self.label_table:
            sys.exit(52)
        else:
            self.label_table[name] = IP
            return True

    def get_label(self, name):
        if name not in self.label_table:
            sys.exit(52)
        else:
            return self.label_table[name]


def Main():
    """Main entry point of the program.
        Used to predefine all classes and functions before execution."""
    symtable = Symtable()
    interpet = Interpret()

    parser = Parser()
    parser.foo(interpet, symtable)

if __name__ == '__main__':
    Main()
else:
    Main()
