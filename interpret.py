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
        "CREATEFRAME"
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

    def do_MOVE(self, arg1, arg2):
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
