import xml.etree.ElementTree as ET
import sys

class Parser:
    """Main program controller, parses instructions out of XML file."""

    IP = 1
    tree = ET.parse('test_inputs\\simple.xml')
    root = tree.getroot()
    # contains:
    #     DEFVAR GF@a
    #     DEFVAR GF@b
    #     DEFVAR GF@c
    #     MOVE GF@a int@2
    #     MOVE GF@b int@3
    #     ADD GF@c GF@a GF@b
    #     WRITE GF@c

    def foo(self, interpret, symtable):
        """Reads instructions from input and executes them."""

        instructions = {
            "MOVE":interpret.do_MOVE,
            "DEFVAR":interpret.do_DEFVAR,
            "ADD":interpret.do_Arithmetic,
            "SUB":interpret.do_Arithmetic,
            "MUL":interpret.do_Arithmetic,
            "IDIV":interpret.do_Arithmetic,
            "WRITE":interpret.do_WRITE,
        }

        instruction_found = True

        while (instruction_found):
            instruction = self.get_instruction(self.IP)

            # if the next instruction isn't found, program terminates.
            if (instruction == None):
                instruction_found = False
                continue

            instructions[instruction.attrib['opcode']](instruction, symtable)
            # print("IP - ", self.IP)
            self.IP += 1

    def get_instruction(self, ip):
        """Tries to find the instruction with the order == ip.
        Returns the instruction if it's found and None otherwise."""

        for instruction in self.root.findall('instruction'):
            if instruction.attrib['order'] == str(ip):
                return instruction
        return None

    def change_ip(self, IP):
        """Changes the instruction pointer value after one of the
        JUMP instructions."""
        self.IP = IP
        return True

class Interpret:
    """Interprets(executes) code parsed out of XML by Parser."""

    def do_DEFVAR(self, instruction, symtable):
        return symtable.define_var(instruction[0].text)

    def do_MOVE(self, instruction, symtable):
        # undefined variable
        if (instruction[0].attrib['type'] != 'var' or symtable.check_defined_var(instruction[0].text) == False):
            sys.exit(54)

        type = instruction[1].attrib['type']
        value = None

        if (type == 'var'):
            type = symtable.get_var(instruction[1].text)
            value = type[1]
            type = type[0]
        elif type == 'int':
            value = int(instruction[1].text)
        else:
            value = instruction[1].text

        return symtable.set_var(instruction[0].text, type, value)

    def do_ADD(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 + value2)

    def do_SUB(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 - value2)

    def do_MUL(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 * value2)

    def do_IDIV(self, symtable, name, value1, value2, type):
        return symtable.set_var(name, type, value1 // value2)

    def do_Arithmetic(self, instruction, symtable):
        """All the typechecking and defchecking is done in one function,
        arithmetic computation is done in separate functions at the end."""

        # undefined variable
        if (instruction[0].attrib['type'] != 'var' or symtable.check_defined_var(instruction[0].text) == False):
            sys.exit(54)

        type1 = instruction[1].attrib['type']
        value1 = None

        if (type1 == 'var'):
            value1 = symtable.get_var(instruction[1].text)
            type1 = value1[0]
            if (type1 != 'int'):
                sys.exit(57)
            value1 = value1[1]
        elif type1 == 'int':
            value1 = int(instruction[1].text)
        else:
            sys.exit(57)

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
            return self.do_IDIV(symtable, instruction[0].text, value1, value2, type1)
        else:
            sys.exit(32)

    def do_WRITE(self, instruction, symtable):
        result = None

        if (instruction[0].attrib['type'] == 'var'):
            result = str(symtable.get_var(instruction[0].text)[1])
        else:
            result = instruction[0].text

        print(result)
        return True





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

    def do_PUSHS(self, instruction):        #, symb):
        return True

    def do_POPS(self, instruction):         #, var):
        return True

    def do_LT(self, instruction):           #, var, symb1, symb2):
        return True

    def do_GT(self, instruction):           #, var, symb1, symb2):
        return True

    def do_EQ(self, instruction):           #, var, symb1, symb2):
        return True

    def do_AND(self, instruction):          #, var, symb1, symb2):
        return True

    def do_OR(self, instruction):           #, var, symb1, symb2):
        return True

    def do_NOT(self, instruction):          #, var, symb):
        return True

    def do_INT2CHAR(self, instruction):     #, var, symb):
        return True

    def do_STRI2INT(self, instruction):     #, var, symb1, symb2):
        return True

    def do_READ(self, instruction):         #, var, symb1, symb2):
        return True

    def do_CONCAT(self, instruction):       #, var, symb1, symb2):
        return True

    def do_STRLEN(self, instruction):       #, var, symb):
        return True

    def do_GETCHAR(self, instruction):      #, var, symb1, symb2):
        return True

    def do_SETCHAR(self, instruction):      #, var, symb1, symb2):
        return True

    def do_TYPE(self, instruction):         #, var, symb):
        return True

    def do_LABEL(self, instruction):        #, label):
        return True

    def do_JUMP(self, instruction):         #, label):
        return True

    def do_JUMPIFEQ(self, instruction):     #, label, symb1, symb2):
        return True

    def do_JUMPIFNEQ(self, instruction):    #, label, symb1, symb2):
        return True

    def do_EXIT(self, instruction):         #, symb):
        return True

    def do_DPRINT(self, instruction):       #, symb):
        return True

    def do_BREAK(self, instruction):        #):
        return True



class Symtable:
    """Contains and manages all information about variables and labels."""

    # currently doesn't support frames
    var_table = {}      # { 'var_table':('type', 'value') }

    # Not implemented:
    LF_table = {}      # { 'var_table':('type', 'value') }
    GF_table = {}       # { 'var_table':('type', 'value') }
    TF_table = {}       # { 'var_table':('type', 'value') }
    label_table = {}    # { 'label_table':'instruction_pointer' }

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
            self.var_table[name] = ('nil', 'nil')
            return True

    def set_var(self, name, type, value):
        if name in self.var_table:
            self.var_table[name] = (type, value)
            return True
        else:
            return False

    def get_var(self, name):
        if name not in self.var_table:
            sys.exit(54)
        else:
            return self.var_table[name]

    def check_defined_label(self, name):
        return name in self.label_table

    def define_label(self, name, IP):
        if name in self.label_table:
            return False
        else:
            self.label_table[name] = IP
            return True

    def get_label(self, name):
        if name not in self.label_table:
            return False
        else:
            return self.label_table[name]


def Main():
    symtable = Symtable()
    interpet = Interpret()

    parser = Parser()
    parser.foo(interpet, symtable)

if __name__ == '__main__':
    Main()
