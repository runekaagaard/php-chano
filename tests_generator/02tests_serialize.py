# -*- coding: utf-8 -*-
import datetime
import sys
from django.template.defaultfilters import *
from collections import deque
from phpserialize import *
from pprint import pprint as p
import codecs

# Make piping work with utf8.
reload(sys)
sys.setdefaultencoding('utf8')
sys.stdout = codecs.getwriter('utf8')(sys.stdout)
sys.stderr = codecs.getwriter('utf8')(sys.stderr)

IN = {}
TESTS = []

def create_test(*args):
    tmp = deque(IN['v'])
    TESTS.append({
        'filter': tmp.popleft(),
        'input': list(tmp),
        'output': args[1]
    })

def set_args(*args): IN['v'] = list(args)

class DefaultFiltersTests():
    def test_floatformat(self):
        create_test(set_args('floatformat', 7.7), u'7.7')
        create_test(set_args('floatformat', 7.0), u'7')
        create_test(set_args('floatformat', 0.7), u'0.7')
        create_test(set_args('floatformat', 0.07), u'0.1')
        create_test(set_args('floatformat', 0.007), u'0.0')
        create_test(set_args('floatformat', 0.0), u'0')
        create_test(set_args('floatformat', 7.7, 3), u'7.700')
        create_test(set_args('floatformat', 6.000000, 3), u'6.000')
        create_test(set_args('floatformat', 6.200000, 3), u'6.200')
        create_test(set_args('floatformat', 6.200000, -3), u'6.200')
        create_test(set_args('floatformat', 13.1031, -3), u'13.103')
        create_test(set_args('floatformat', 11.1197, -2), u'11.12')
        create_test(set_args('floatformat', 11.0000, -2), u'11')
        create_test(set_args('floatformat', 11.000001, -2), u'11.00')
        create_test(set_args('floatformat', 8.2798, 3), u'8.280')
        create_test(set_args('floatformat', u'foo'), u'')
        create_test(set_args('floatformat', 13.1031, u'bar'), u'13.1031')
        create_test(set_args('floatformat', 18.125, 2), u'18.13')
        create_test(set_args('floatformat', u'foo', u'bar'), u'')
        create_test(set_args('floatformat', u'¿Cómo esta usted?'), u'')
        create_test(set_args('floatformat', None), u'')

        create_test(set_args('floatformat', 11.000001, -2), u'11.00')
    
    def test_addslashes(self):
        create_test(set_args('addslashes', u'"double quotes" and \'single quotes\''),
                          u'\\"double quotes\\" and \\\'single quotes\\\'')

        create_test(set_args('addslashes', ur'\ : backslashes, too'),
                          u'\\\\ : backslashes, too')
    
    def test_capfirst(self):
        create_test(set_args('capfirst', u'hello world'), u'Hello world')
    
    def test_escapejs(self):
        create_test(set_args('escapejs', u'"double quotes" and \'single quotes\''),
            u'\\u0022double quotes\\u0022 and \\u0027single quotes\\u0027')
        create_test(set_args('escapejs', ur'\ : backslashes, too'),
            u'\\u005C : backslashes, too')
        create_test(set_args('escapejs', u'and lots of whitespace: \r\n\t\v\f\b'),
            u'and lots of whitespace: \\u000D\\u000A\\u0009\\u000B\\u000C\\u0008')
        create_test(set_args('escapejs', ur'<script>and this</script>'),
            u'\\u003Cscript\\u003Eand this\\u003C/script\\u003E')
        create_test(set_args('escapejs', u'paragraph separator:\u2029and line separator:\u2028'),
            u'paragraph separator:\\u2029and line separator:\\u2028')

    def test_fix_ampersands(self):
        create_test(set_args('fix_ampersands', u'Jack & Jill & Jeroboam'),
                          u'Jack &amp; Jill &amp; Jeroboam')

    def test_linenumbers(self):
        create_test(set_args('linenumbers', u'line 1\nline 2'),
                          u'1. line 1\n2. line 2')
        create_test(set_args('linenumbers', u'\n'.join([u'x'] * 10)),
                          u'01. x\n02. x\n03. x\n04. x\n05. x\n06. x\n07. '\
                          u'x\n08. x\n09. x\n10. x')
    
    def test_lower(self):
        create_test(set_args('lower', 'TEST'), u'test')

        # uppercase E umlaut
        create_test(set_args('lower', u'\xcb'), u'\xeb')

    def test_make_list(self):
        create_test(set_args('make_list', 'abc'), [u'a', u'b', u'c'])
        create_test(set_args('make_list', 1234), [u'1', u'2', u'3', u'4'])

    def test_slugify(self):
        create_test(set_args('slugify', ' Jack & Jill like numbers 1,2,3 and 4 and'\
            ' silly characters ?%.$!/'),
            u'jack-jill-like-numbers-123-and-4-and-silly-characters')

        create_test(set_args('slugify', u"Un \xe9l\xe9phant \xe0 l'or\xe9e du bois"),
                          u'un-elephant-a-loree-du-bois')

    def test_stringformat(self):
        create_test(set_args('stringformat', 1, u'03d'), u'001')
        create_test(set_args('stringformat', 1, u'z'), u'')

    def test_title(self):
        create_test(set_args('title', 'a nice title, isn\'t it?'),
                          u"A Nice Title, Isn't It?")
        create_test(set_args('title', u'discoth\xe8que'), u'Discoth\xe8que')
    
    def test_truncatewords(self):
        create_test(set_args('truncatewords', u'A sentence with a few words in it', 1), u'A ...')
        create_test(set_args('truncatewords', u'A sentence with a few words in it', 5),
            u'A sentence with a few ...')
        create_test(set_args('truncatewords', u'A sentence with a few words in it', 100),
            u'A sentence with a few words in it')
        create_test(set_args('truncatewords', u'A sentence with a few words in it',
            'not a number'), u'A sentence with a few words in it')

    def test_truncatewords_html(self):
        create_test(set_args('truncatewords_html', 
            u'<p>one <a href="#">two - three <br>four</a> five</p>', 0), u'')
        create_test(set_args('truncatewords_html', u'<p>one <a href="#">two - '\
            u'three <br>four</a> five</p>', 2),
            u'<p>one <a href="#">two ...</a></p>')
        create_test(set_args('truncatewords_html', 
            u'<p>one <a href="#">two - three <br>four</a> five</p>', 4),
            u'<p>one <a href="#">two - three <br>four ...</a></p>')
        create_test(set_args('truncatewords_html', 
            u'<p>one <a href="#">two - three <br>four</a> five</p>', 5),
            u'<p>one <a href="#">two - three <br>four</a> five</p>')
        create_test(set_args('truncatewords_html', 
            u'<p>one <a href="#">two - three <br>four</a> five</p>', 100),
            u'<p>one <a href="#">two - three <br>four</a> five</p>')
        create_test(set_args('truncatewords_html', 
            u'\xc5ngstr\xf6m was here', 1), u'\xc5ngstr\xf6m ...')

    def test_upper(self):
        create_test(set_args('upper', u'Mixed case input'), u'MIXED CASE INPUT')
        # lowercase e umlaut
        create_test(set_args('upper', u'\xeb'), u'\xcb')

    def test_urlencode(self):
        create_test(set_args('urlencode', u'fran\xe7ois & jill'),
                          u'fran%C3%A7ois%20%26%20jill')
        create_test(set_args('urlencode', 1), u'1')

    def test_iriencode(self):
        create_test(set_args('iriencode', u'S\xf8r-Tr\xf8ndelag'),
                          u'S%C3%B8r-Tr%C3%B8ndelag')
        create_test(set_args('iriencode', urlencode(u'fran\xe7ois & jill')),
                          u'fran%C3%A7ois%20%26%20jill')
    
    def test_urlizetrunc(self):
        create_test(set_args('urlizetrunc', u'http://short.com/', 20), u'<a href='\
            u'"http://short.com/" rel="nofollow">http://short.com/</a>')

        create_test(set_args('urlizetrunc', u'http://www.google.co.uk/search?hl=en'\
            u'&q=some+long+url&btnG=Search&meta=', 20), u'<a href="http://'\
            u'www.google.co.uk/search?hl=en&q=some+long+url&btnG=Search&'\
            u'meta=" rel="nofollow">http://www.google...</a>')

        create_test(set_args('urlizetrunc', 'http://www.google.co.uk/search?hl=en'\
            u'&q=some+long+url&btnG=Search&meta=', 20), u'<a href="http://'\
            u'www.google.co.uk/search?hl=en&q=some+long+url&btnG=Search'\
            u'&meta=" rel="nofollow">http://www.google...</a>')

        # Check truncating of URIs which are the exact length
        uri = 'http://31characteruri.com/test/'
        create_test(set_args('len', uri), 31)

        create_test(set_args('urlizetrunc', uri, 31),
            u'<a href="http://31characteruri.com/test/" rel="nofollow">'\
            u'http://31characteruri.com/test/</a>')

        create_test(set_args('urlizetrunc', uri, 30),
            u'<a href="http://31characteruri.com/test/" rel="nofollow">'\
            u'http://31characteruri.com/t...</a>')

        create_test(set_args('urlizetrunc', uri, 2),
            u'<a href="http://31characteruri.com/test/"'\
            u' rel="nofollow">...</a>')

    def test_urlize(self):
        # Check normal urlize
        create_test(set_args('urlize', 'http://google.com'),
            u'<a href="http://google.com" rel="nofollow">http://google.com</a>')
        create_test(set_args('urlize', 'http://google.com/'),
            u'<a href="http://google.com/" rel="nofollow">http://google.com/</a>')
        create_test(set_args('urlize', 'www.google.com'),
            u'<a href="http://www.google.com" rel="nofollow">www.google.com</a>')
        create_test(set_args('urlize', 'djangoproject.org'),
            u'<a href="http://djangoproject.org" rel="nofollow">djangoproject.org</a>')
        create_test(set_args('urlize', 'info@djangoproject.org'),
            u'<a href="mailto:info@djangoproject.org">info@djangoproject.org</a>')

        # Check urlize with https addresses
        create_test(set_args('urlize', 'https://google.com'),
            u'<a href="https://google.com" rel="nofollow">https://google.com</a>')

    def test_wordcount(self):
        create_test(set_args('wordcount', ''), 0)
        create_test(set_args('wordcount', u'oneword'), 1)
        create_test(set_args('wordcount', u'lots of words'), 3)

        create_test(set_args('wordwrap', u'this is a long paragraph of text that '\
            u'really needs to be wrapped I\'m afraid', 14),
            u"this is a long\nparagraph of\ntext that\nreally needs\nto be "\
            u"wrapped\nI'm afraid")

        create_test(set_args('wordwrap', u'this is a short paragraph of text.\n  '\
            u'But this line should be indented', 14),
            u'this is a\nshort\nparagraph of\ntext.\n  But this\nline '\
            u'should be\nindented')

        create_test(set_args('wordwrap', u'this is a short paragraph of text.\n  '\
            u'But this line should be indented',15), u'this is a short\n'\
            u'paragraph of\ntext.\n  But this line\nshould be\nindented')

    def test_rjust(self):
        create_test(set_args('ljust', u'test', 10), u'test      ')
        create_test(set_args('ljust', u'test', 3), u'test')
        create_test(set_args('rjust', u'test', 10), u'      test')
        create_test(set_args('rjust', u'test', 3), u'test')

    def test_center(self):
        create_test(set_args('center', u'test', 6), u' test ')

    def test_cut(self):
        create_test(set_args('cut', u'a string to be mangled', 'a'),
                          u' string to be mngled')
        create_test(set_args('cut', u'a string to be mangled', 'ng'),
                          u'a stri to be maled')
        create_test(set_args('cut', u'a string to be mangled', 'strings'),
                          u'a string to be mangled')
    
    def test_force_escape(self):
        create_test(set_args('force_escape', u'<some html & special characters > here'),
            u'&lt;some html &amp; special characters &gt; here')
        create_test(set_args('force_escape', u'<some html & special characters > here ĐÅ€£'),
            u'&lt;some html &amp; special characters &gt; here'\
            u' \u0110\xc5\u20ac\xa3')

    def test_linebreaks(self):
        create_test(set_args('linebreaks', u'line 1'), u'<p>line 1</p>')
        create_test(set_args('linebreaks', u'line 1\nline 2'),
                          u'<p>line 1<br />line 2</p>')

    def test_removetags(self):
        create_test(set_args('removetags', u'some <b>html</b> with <script>alert'\
            u'("You smell")</script> disallowed <img /> tags', 'script img'),
            u'some <b>html</b> with alert("You smell") disallowed  tags')
        create_test(set_args('striptags', u'some <b>html</b> with <script>alert'\
            u'("You smell")</script> disallowed <img /> tags'),
            u'some html with alert("You smell") disallowed  tags')
    """
    def test_dictsort(self):
        
        sorted_dicts = dictsort([{'age': 23, 'name': 'Barbara-Ann'},
                                 {'age': 63, 'name': 'Ra Ra Rasputin'},
                                 {'name': 'Jonny B Goode', 'age': 18}], 'age')

        self.assertEqual([sorted(dict.items()) for dict in sorted_dicts],
            [[('age', 18), ('name', 'Jonny B Goode')],
             [('age', 23), ('name', 'Barbara-Ann')],
             [('age', 63), ('name', 'Ra Ra Rasputin')]])

    def test_dictsortreversed(self):
        sorted_dicts = dictsortreversed([{'age': 23, 'name': 'Barbara-Ann'},
                                         {'age': 63, 'name': 'Ra Ra Rasputin'},
                                         {'name': 'Jonny B Goode', 'age': 18}],
                                        'age')

        self.assertEqual([sorted(dict.items()) for dict in sorted_dicts],
            [[('age', 63), ('name', 'Ra Ra Rasputin')],
             [('age', 23), ('name', 'Barbara-Ann')],
             [('age', 18), ('name', 'Jonny B Goode')]])
    """
    def test_first(self):
        create_test(set_args('first', [0,1,2]), 0)
        create_test(set_args('first', u''), u'')
        create_test(set_args('first', u'test'), u't')

    def test_join(self):
        create_test(set_args('join', [0,1,2], u'glue'), u'0glue1glue2')

    def test_length(self):
        create_test(set_args('length', u'1234'), 4)
        create_test(set_args('length', [1,2,3,4]), 4)
        create_test(set_args('length_is', [], 0), True)
        create_test(set_args('length_is', [], 1), False)
        create_test(set_args('length_is', 'a', 1), True)
        create_test(set_args('length_is', u'a', 10), False)

    def test_slice(self):
        create_test(set_args('slice_', u'abcdefg', u'0'), u'')
        create_test(set_args('slice_', u'abcdefg', u'1'), u'a')
        create_test(set_args('slice_', u'abcdefg', u'-1'), u'abcdef')
        create_test(set_args('slice_', u'abcdefg', u'1:2'), u'b')
        create_test(set_args('slice_', u'abcdefg', u'1:3'), u'bc')
        create_test(set_args('slice_', u'abcdefg', u'0::2'), u'aceg')

    def test_unordered_list(self):
        create_test(set_args('unordered_list', [u'item 1', u'item 2']),
            u'\t<li>item 1</li>\n\t<li>item 2</li>')
        create_test(set_args('unordered_list', [u'item 1', [u'item 1.1']]),
            u'\t<li>item 1\n\t<ul>\n\t\t<li>item 1.1</li>\n\t</ul>\n\t</li>')

        create_test(set_args('unordered_list', [u'item 1', [u'item 1.1', u'item1.2'], u'item 2']),
            u'\t<li>item 1\n\t<ul>\n\t\t<li>item 1.1</li>\n\t\t<li>item1.2'\
            u'</li>\n\t</ul>\n\t</li>\n\t<li>item 2</li>')

        create_test(set_args('unordered_list', [u'item 1', [u'item 1.1', [u'item 1.1.1',
                                                      [u'item 1.1.1.1']]]]),
            u'\t<li>item 1\n\t<ul>\n\t\t<li>item 1.1\n\t\t<ul>\n\t\t\t<li>'\
            u'item 1.1.1\n\t\t\t<ul>\n\t\t\t\t<li>item 1.1.1.1</li>\n\t\t\t'\
            u'</ul>\n\t\t\t</li>\n\t\t</ul>\n\t\t</li>\n\t</ul>\n\t</li>')

        create_test(set_args('unordered_list', 
            ['States', ['Kansas', ['Lawrence', 'Topeka'], 'Illinois']]),
            u'\t<li>States\n\t<ul>\n\t\t<li>Kansas\n\t\t<ul>\n\t\t\t<li>'\
            u'Lawrence</li>\n\t\t\t<li>Topeka</li>\n\t\t</ul>\n\t\t</li>'\
            u'\n\t\t<li>Illinois</li>\n\t</ul>\n\t</li>')

        class ULItem(object):
            def __init__(self, title):
              self.title = title
            def __unicode__(self):
                return u'ulitem-%s' % str(self.title)

        a = ULItem('a').__unicode__()
        b = ULItem('b').__unicode__()
        create_test(set_args('unordered_list', [a,b]),
                          u'\t<li>ulitem-a</li>\n\t<li>ulitem-b</li>')

        # Old format for unordered lists should still work
        create_test(set_args('unordered_list', [u'item 1', []]), u'\t<li>item 1</li>')

        create_test(set_args('unordered_list', [u'item 1', [[u'item 1.1', []]]]),
            u'\t<li>item 1\n\t<ul>\n\t\t<li>item 1.1</li>\n\t</ul>\n\t</li>')

        create_test(set_args('unordered_list', [u'item 1', [[u'item 1.1', []],
            [u'item 1.2', []]]]), u'\t<li>item 1\n\t<ul>\n\t\t<li>item 1.1'\
            u'</li>\n\t\t<li>item 1.2</li>\n\t</ul>\n\t</li>')

        create_test(set_args('unordered_list', ['States', [['Kansas', [['Lawrence',
            []], ['Topeka', []]]], ['Illinois', []]]]), u'\t<li>States\n\t'\
            u'<ul>\n\t\t<li>Kansas\n\t\t<ul>\n\t\t\t<li>Lawrence</li>'\
            u'\n\t\t\t<li>Topeka</li>\n\t\t</ul>\n\t\t</li>\n\t\t<li>'\
            u'Illinois</li>\n\t</ul>\n\t</li>')
    
    def test_add(self):
        create_test(set_args('add', u'1', u'2'), 3)

    def test_get_digit(self):
        create_test(set_args('get_digit', 123, 1), 3)
        create_test(set_args('get_digit', 123, 2), 2)
        create_test(set_args('get_digit', 123, 3), 1)
        create_test(set_args('get_digit', 123, 4), 0)
        create_test(set_args('get_digit', 123, 0), 123)
        create_test(set_args('get_digit', u'xyz', 0), u'xyz')

    def test_date(self):
        # real testing of date() is in dateformat.py
        create_test(set_args('date', datetime.datetime(2005, 12, 29).strftime('%s'), u"d F Y"),
                          u'29 December 2005')
        create_test(set_args('date', datetime.datetime(2005, 12, 29).strftime('%s'), ur'jS o\f F'),
                          u'29th of December')

    def test_time(self):
        # real testing of time() is done in dateformat.py
        create_test(set_args('time', 13, u"h"), u'01')
        create_test(set_args('time', 0, u"h"), u'12')

#    def test_timesince(self):
#        # real testing is done in timesince.py, where we can provide our own 'now'
#        create_test(set_args('timesince', datetime.datetime.now() - datetime.timedelta(1)),
#            u'1 day')
#
#        create_test(set_args('timesince', datetime.datetime(2005, 12, 29),
#                      datetime.datetime(2005, 12, 30)),
#            u'1 day')
#
#    def test_timeuntil(self):
#        create_test(set_args('timeuntil', datetime.datetime.now() + datetime.timedelta(1)),
#            u'1 day')
#
#        create_test(set_args('timeuntil', datetime.datetime(2005, 12, 30),
#                                    datetime.datetime(2005, 12, 29)),
#                          u'1 day')

    def test_default(self):
        create_test(set_args('default', u"val", u"default"), u'val')
        create_test(set_args('default', None, u"default"), u'default')
        create_test(set_args('default', u'', u"default"), u'default')

    def test_if_none(self):
        create_test(set_args('default_if_none', u"val", u"default"), u'val')
        create_test(set_args('default_if_none', None, u"default"), u'default')
        create_test(set_args('default_if_none', u'', u"default"), u'')

    def test_divisibleby(self):
        create_test(set_args('divisibleby', 4, 2), True)
        create_test(set_args('divisibleby', 4, 3), False)

    def test_yesno(self):
        create_test(set_args('yesno', True), u'yes')
        create_test(set_args('yesno', False), u'no')
        create_test(set_args('yesno', None), u'maybe')
        create_test(set_args('yesno', True, u'certainly,get out of town,perhaps'),
                          u'certainly')
        create_test(set_args('yesno', False, u'certainly,get out of town,perhaps'),
                          u'get out of town')
        create_test(set_args('yesno', None, u'certainly,get out of town,perhaps'),
                          u'perhaps')
        create_test(set_args('yesno', None, u'certainly,get out of town'),
                          u'get out of town')

    def test_filesizeformat(self):
        create_test(set_args('filesizeformat', 1023), u'1023 bytes')
        create_test(set_args('filesizeformat', 1024), u'1.0 KB')
        create_test(set_args('filesizeformat', 10*1024), u'10.0 KB')
        create_test(set_args('filesizeformat', 1024*1024-1), u'1024.0 KB')
        create_test(set_args('filesizeformat', 1024*1024), u'1.0 MB')
        create_test(set_args('filesizeformat', 1024*1024*50), u'50.0 MB')
        create_test(set_args('filesizeformat', 1024*1024*1024-1), u'1024.0 MB')
        create_test(set_args('filesizeformat', 1024*1024*1024), u'1.0 GB')
        create_test(set_args('filesizeformat', 1024*1024*1024*1024), u'1.0 TB')
        create_test(set_args('filesizeformat', 1024*1024*1024*1024*1024), u'1.0 PB')
        create_test(set_args('filesizeformat', 1024*1024*1024*1024*1024*2000),
                          u'2000.0 PB')
        create_test(set_args('filesizeformat', ""), u'0 bytes')
        create_test(set_args('filesizeformat', u"\N{GREEK SMALL LETTER ALPHA}"),
                          u'0 bytes')

    def test_pluralize(self):
        create_test(set_args('pluralize', 1), u'')
        create_test(set_args('pluralize', 0), u's')
        create_test(set_args('pluralize', 2), u's')
        create_test(set_args('pluralize', [1]), u'')
        create_test(set_args('pluralize', []), u's')
        create_test(set_args('pluralize', [1,2,3]), u's')
        create_test(set_args('pluralize', 1,u'es'), u'')
        create_test(set_args('pluralize', 0,u'es'), u'es')
        create_test(set_args('pluralize', 2,u'es'), u'es')
        create_test(set_args('pluralize', 1,u'y,ies'), u'y')
        create_test(set_args('pluralize', 0,u'y,ies'), u'ies')
        create_test(set_args('pluralize', 2,u'y,ies'), u'ies')
        create_test(set_args('pluralize', 0,u'y,ies,error'), u'')

    def test_phone2numeric(self):
        create_test(set_args('phone2numeric', u'0800 flowers'), u'0800 3569377')

    def test_non_string_input(self):
        # Filters shouldn't break if passed non-strings
        create_test(set_args('addslashes', 123), u'123')
        create_test(set_args('linenumbers', 123), u'1. 123')
        create_test(set_args('lower', 123), u'123')
        create_test(set_args('make_list', 123), [u'1', u'2', u'3'])
        create_test(set_args('slugify', 123), u'123')
        create_test(set_args('title', 123), u'123')
        create_test(set_args('truncatewords', 123, 2), u'123')
        create_test(set_args('upper', 123), u'123')
        create_test(set_args('urlencode', 123), u'123')
        create_test(set_args('urlize', 123), u'123')
        create_test(set_args('urlizetrunc', 123, 1), u'123')
        create_test(set_args('wordcount', 123), 1)
        create_test(set_args('wordwrap', 123, 2), u'123')
        create_test(set_args('ljust', '123', 4), u'123 ')
        create_test(set_args('rjust', '123', 4), u' 123')
        create_test(set_args('center', '123', 5), u' 123 ')
        create_test(set_args('center', '123', 6), u' 123  ')
        create_test(set_args('cut', 123, '2'), u'13')
        create_test(set_args('escape', 123), u'123')
        create_test(set_args('linebreaks', 123), u'<p>123</p>')
        create_test(set_args('linebreaksbr', 123), u'123')
        create_test(set_args('removetags', 123, 'a'), u'123')
        create_test(set_args('striptags', 123), u'123')
        
if __name__ == '__main__':
        [getattr(DefaultFiltersTests(), m)() for m in dir(DefaultFiltersTests) 
            if m.find('test') == 0]
        print dumps(TESTS)
        import json
        print json.dumps(TESTS)
