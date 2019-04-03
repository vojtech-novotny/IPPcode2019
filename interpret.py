import xml.etree.ElementTree as ET

class Parser:
    """Main program controller, parses instructions out of XML file."""

    IP = 1
    tree = ET.parse('output.xml')
    root = tree.getroot()

    interpret = Interpret()
    symtable = Symtable()

    instructions = {
        "MOVE":interpret.do_MOVE,
        "CREATEFRAME":interpet.do_CREATEFRAME,
        "PUSHFRAME":interpret.do_PUSHFRAME,
        "POPFRAME":interpret.do_POPFRAME,
        "DEFVAR":interpret.do_DEFVAR,
        "CALL":interpret.do_CALL,
        "RETURN":interpret.do_RETURN,
        "PUSHS":interpret.do_PUSHS,
        "POPS":interpret.do_POPS,
        "ADD":interpret.do_ADD,
        "SUB":interpret.do_SUB,
        "MUL":interpret.do_MUL,
        "IDIV":interpret.do_IDIV,
        "LT":interpret.do_LT,
        "GT":interpret.do_GT,
        "EQ":interpret.do_EQ,
        "AND":interpret.do_AND,
        "OR":interpret.do_OR,
        "NOT":interpret.do_NOT,
        "INT2CHAR":interpret.do_INT2CHAR,
        "STRI2INT":interpret.do_STRI2INT,
        "READ":interpret.do_READ,
        "WRITE":interpret.do_WRITE,
        "CONCAT":interpret.do_CONCAT,
        "STRLEN":interpret.do_STRLEN,
        "GETCHAR":interpret.do_GETCHAR,
        "SETCHAR":interpret.do_SETCHAR,
        "TYPE":interpret.do_TYPE,
        "LABEL":interpret.do_LABEL,
        "JUMP":interpret.do_JUMP,
        "JUMPIFEQ":interpret.do_JUMPIFEQ,
        "JUMPIFNEQ":interpret.do_JUMPIFEQ,
        "EXIT":interpret.do_EXIT,
        "DPRINT":interpret.do_DPRINT,
        "BREAK":interpret.do_BREAK,
    }

    def main_loop(self):

        instruction = None

        while True:
            instruction = get_instruction(IP)
            IP++

        return True

    def get_instruction(self, ip):
        return (self.root[ip].attrib['opcode'], root[ip])

    def change_ip(self, IP):
        return True

class Interpret:
    """Interprets(executes) code parsed out of XML by Parser."""

    def do_MOVE(self, var, symb):
        return True

    def do_CREATEFRAME(self):
        return True

    def do_PUSHFRAME(self):
        return True

    def do_POPFRAME(self):
        return True

    def do_DEFVAR(self, var):
        return True

    def do_CALL(self, label):
        return True

    def do_RETURN(self):
        return True

    def do_PUSHS(self, symb):
        return True

    def do_POPS(self, var):
        return True

    def do_ADD(self, var, symb1, symb2):
        return True

    def do_SUB(self, var, symb1, symb2):
        return True

    def do_MUL(self, var, symb1, symb2):
        return True

    def do_IDIV(self, var, symb1, symb2):
        return True

    def do_LT(self, var, symb1, symb2):
        return True

    def do_GT(self, var, symb1, symb2):
        return True

    def do_EQ(self, var, symb1, symb2):
        return True

    def do_AND(self, var, symb1, symb2):
        return True

    def do_OR(self, var, symb1, symb2):
        return True

    def do_NOT(self, var, symb):
        return True

    def do_INT2CHAR(self, var, symb):
        return True

    def do_STRI2INT(self, var, symb1, symb2):
        return True

    def do_READ(self, var, symb1, symb2):
        return True

    def do_WRITE(self, symb):
        return True

    def do_CONCAT(self, var, symb1, symb2):
        return True

    def do_STRLEN(self, var, symb):
        return True

    def do_GETCHAR(self, var, symb1, symb2):
        return True

    def do_SETCHAR(self, var, symb1, symb2):
        return True

    def do_TYPE(self, var, symb):
        return True

    def do_LABEL(self, label):
        return True

    def do_JUMP(self, label):
        return True

    def do_JUMPIFEQ(self, label, symb1, symb2):
        return True

    def do_JUMPIFNEQ(self, label, symb1, symb2):
        return True

    def do_EXIT(self, symb):
        return True

    def do_DPRINT(self, symb):
        return True

    def do_BREAK(self):
        return True



class Symtable:
    """Contains and manages all information about variables and labels."""

    var_table = { 'var_table':('type', 'value') }
    label_table = { 'label_table':'instruction_pointer' }

    def check_defined_var(self, name):
        return name in self.var_table

    def check_defined_label(self, name):
        return name in self.label_table

    def check_type(self, name):
        if name not in self.var_table:
            return False
        else:
            return self.var_table[name][0]

    def define_var(self, name):
        if name in self.var_table:
            return False
        else:
            self.var_table[name] = ('nil', 'nil')
            return True

    def define_label(self, name, IP):
        if name in self.label_table:
            return False
        else:
            self.label_table[name] = IP
            return True

    def set_var(self, name, type, value):
        if name in self.var_table:
            self.var_table[name] = (type, value)
            return True
        else:
            return False

    def get_var(self, name):
        if name not in self.var_table:
            return False
        else:
            return self.var_table[name]

    def get_label(self, name):
        if name not in self.label_table:
            return False
        else:
            return self.label_table[name]
