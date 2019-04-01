import xml.etree.ElementTree as ET

class Parser:
    """Main program controller, parses instructions out of XML file."""

    ip = 0

    def main_loop():
        return True

    def get_instruction(ip):
        return True

    def change_ip(IP):
        return True

class Interpret:
    """Interprets(executes) code parsed out of XML by Parser."""

    def interpret_MOVE(arg1, arg2):
        return True


class Symtable:
    """Contains and manages all information about variables and labels."""

    var_table = { 'var_table':('type', 'value') }
    label_table = { 'label_table':'instruction_pointer' }

    def check_defined_var(name):
        return True

    def check_defined_label(name):
        return True

    def check_type(name):
        return True

    def define_var(name):
        return True

    def define_label(name, IP):
        return True

    def set_var(name, type, value):
        return True

    def get_var(name):
        return (type, value)

    def get_label(name):
        return IP
        
