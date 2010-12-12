# -*- coding: utf-8 -*-
from django.template.defaultfilters import _js_escapes
from json import dumps
import sys
import codecs

# Make piping work with utf8.
reload(sys)
sys.setdefaultencoding('utf8')
sys.stdout = codecs.getwriter('utf8')(sys.stdout)
sys.stderr = codecs.getwriter('utf8')(sys.stderr)

from_=[]
to=[]

for j in _js_escapes:
    from_.append(j[0])
    to.append(j[1])

#print dumps(from_)
print dumps(to)