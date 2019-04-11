# IPPcode2019 - interpret.py
*Vojtěch Novotný - xnovot1f*

### Cílová funkcionalita programu
Program by měl interpretovat XML reprezentaci kódu jazyka IPPcode19.

### Skutečná funkcionalita programu
Vše není a tak je zde seznam neimplementovaných instrukcí:
 - DPRINT (dobrovolná instrukce)
 - BREAK (dobrovolná instrukce)
 - CREATEFRAME
 - PUSHFRAME
 - POPFRAME
 - CALL
 - RETURN
 - PUSHS
 - POPS

Jednoduše řečeno, implementovány nejsou ladící instrukce, volání funkcí a zásobníkové instrukce. Neimplementované instrukce program zpracuje (neskončí s chybou), avšak neprovede (instrukce nic nedělají).

Dále nejsou implementovány samotné rámce, což znamená, že všechny proměnné jsou globální a část se jménem rámce je součástí jejich jmen.

### Členění programu.
Program je rozdělen na 3 hlavní třídy:

**Parser** ...
Obsahuje hlavní smyčku programu, kde provádí analýzu XML struktury a získává z ní instrukce. Volá metody třídy Interpret.

**Interpret** ...
Obsahuje metody provádějící samotnout interpretaci. Pro většinu instrukcí je zde právě jedna funkce (Parser má odkazy na tyto funkce uloženy ve slovníku.), pro připady, kdy se několik instrukcí liší pouze operátorem, obstahuje Interpret vždy jednu metodu pro provedení sémantických kontrol operandů s větvením na konci s provedením konkrétních instrukcí (např. aritmetické instrukce či logické instrukce).
Volá metody třídy Symtable.

**Symtable** ...
Obsahuje jednu globální tabulku proměnných ve formě slovníku, kde jméno je klíč a hodnota obsahuje aktuální typ a hodnotu proměnné. Obsahuje také jednu tabulku návěští ve formě slovníku, kde jméno je klíč a hodnota je číslo instrukce (XML atribut order).
Nakonec obsahuje metody pro správu těchto tabulek.

### Zpracování XML
Ke zpracování XML je použita standartní knihovna xml.etree.ElementTree. Jedná se o nejjednodušší knihovnu pro práci s XML bez pokročilejší funkcionality jako například Xpath, ale stačila mi. Problémem by mohlo být, že způsob jaký je použit načte kompletní strukturu XML najednou a tak by při dostatečně velkém vstupním souboru a dostatečně malé operační paměti mohlo dojít k naplnění paměti.

Jako první projde Parser celý program a načte všechny LABEL instrukce a vloží je do tabulky návěští. Poté vyhledává ve struktuře elementy s atributem order rovným aktuální hodnotě čítače instrukcí (ten je potupně inkrementován, případně modifikován skokovými instrukcemi).

### Zpracování argumentů programu
Ke zpracování programových argumentů ('-i', '-s') je využit modulu argparse. Umožňuje jednoduchou správu argumentů a automatické vytvoření nápovědy spouštění programu při předání argumentu '-h' ('--help').


