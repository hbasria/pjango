<?php 

function gettext_noop($str) {
	return $str;
}

####################
# CORE             #
####################
$GLOBALS['SETTINGS']['DEBUG'] = false;
$GLOBALS['SETTINGS']['TEMPLATE_DEBUG'] = false;

# Whether the framework should propagate raw exceptions rather than catching
# them. This is useful under some testing siutations and should never be used
# on a live site.
$GLOBALS['SETTINGS']['DEBUG_PROPAGATE_EXCEPTIONS'] = false;

# Whether to use the "Etag" header. This saves bandwidth but slows down performance.
$GLOBALS['SETTINGS']['USE_ETAGS'] = false;

# People who get code error notifications.
# In the format (('Full Name', 'email@domain.com'), ('Full Name', 'anotheremail@domain.com'))
$GLOBALS['SETTINGS']['ADMINS'] = array();

# Tuple of IP addresses, as strings, that:
#   * See debug comments, when DEBUG is true
#   * Receive x-headers
$GLOBALS['SETTINGS']['INTERNAL_IPS'] = array();

# Local time zone for this installation. All choices can be found here:
# http://en.wikipedia.org/wiki/List_of_tz_zones_by_name (although not all
# systems may support all possibilities).
$GLOBALS['SETTINGS']['TIME_ZONE'] = 'Europe/Istanbul';

# Language code for this installation. All choices can be found here:
# http://www.i18nguy.com/unicode/language-identifiers.html
$GLOBALS['SETTINGS']['LANGUAGE_CODE'] = 'tr';

# Languages we provide translations for, out of the box. The language name
# should be the utf-8 encoded local name for the language.
$GLOBALS['SETTINGS']['LANGUAGES'] = array(
    array('ar', gettext_noop('Arabic')),
    array('bg', gettext_noop('Bulgarian')),
    array('bn', gettext_noop('Bengali')),
    array('bs', gettext_noop('Bosnian')),
    array('ca', gettext_noop('Catalan')),
    array('cs', gettext_noop('Czech')),
    array('cy', gettext_noop('Welsh')),
    array('da', gettext_noop('Danish')),
    array('de', gettext_noop('German')),
    array('el', gettext_noop('Greek')),
    array('en', gettext_noop('English')),
    array('en-gb', gettext_noop('British English')),
    array('es', gettext_noop('Spanish')),
    array('es-ar', gettext_noop('Argentinean Spanish')),
    array('et', gettext_noop('Estonian')),
    array('eu', gettext_noop('Basque')),
    array('fa', gettext_noop('Persian')),
    array('fi', gettext_noop('Finnish')),
    array('fr', gettext_noop('French')),
    array('fy-nl', gettext_noop('Frisian')),
    array('ga', gettext_noop('Irish')),
    array('gl', gettext_noop('Galician')),
    array('he', gettext_noop('Hebrew')),
    array('hi', gettext_noop('Hindi')),
    array('hr', gettext_noop('Croatian')),
    array('hu', gettext_noop('Hungarian')),
    array('id', gettext_noop('Indonesian')),
    array('is', gettext_noop('Icelandic')),
    array('it', gettext_noop('Italian')),
    array('ja', gettext_noop('Japanese')),
    array('ka', gettext_noop('Georgian')),
    array('km', gettext_noop('Khmer')),
    array('kn', gettext_noop('Kannada')),
    array('ko', gettext_noop('Korean')),
    array('lt', gettext_noop('Lithuanian')),
    array('lv', gettext_noop('Latvian')),
    array('mk', gettext_noop('Macedonian')),
    array('mn', gettext_noop('Mongolian')),
    array('nl', gettext_noop('Dutch')),
    array('no', gettext_noop('Norwegian')),
    array('nb', gettext_noop('Norwegian Bokmal')),
    array('nn', gettext_noop('Norwegian Nynorsk')),
    array('pl', gettext_noop('Polish')),
    array('pt', gettext_noop('Portuguese')),
    array('pt-br', gettext_noop('Brazilian Portuguese')),
    array('ro', gettext_noop('Romanian')),
    array('ru', gettext_noop('Russian')),
    array('sk', gettext_noop('Slovak')),
    array('sl', gettext_noop('Slovenian')),
    array('sq', gettext_noop('Albanian')),
    array('sr', gettext_noop('Serbian')),
    array('sr-latn', gettext_noop('Serbian Latin')),
    array('sv', gettext_noop('Swedish')),
    array('ta', gettext_noop('Tamil')),
    array('te', gettext_noop('Telugu')),
    array('th', gettext_noop('Thai')),
    array('tr', gettext_noop('Turkish')),
    array('uk', gettext_noop('Ukrainian')),
    array('vi', gettext_noop('Vietnamese')),
    array('zh-cn', gettext_noop('Simplified Chinese')),
    array('zh-tw', gettext_noop('Traditional Chinese')),
);

# Languages using BiDi (right-to-left) layout
$GLOBALS['SETTINGS']['LANGUAGES_BIDI'] = array("he", "ar", "fa");

# If you set this to False, Django will make some optimizations so as not
# to load the internationalization machinery.
$GLOBALS['SETTINGS']['USE_I18N'] = true;
$GLOBALS['SETTINGS']['LOCALE_PATHS'] = array();
$GLOBALS['SETTINGS']['LANGUAGE_COOKIE_NAME'] = 'pjango_language';

# If you set this to True, Django will format dates, numbers and calendars
# according to user current locale
$GLOBALS['SETTINGS']['USE_L10N'] = false;

# Not-necessarily-technical managers of the site. They get broken link
# notifications and other various e-mails.
$GLOBALS['SETTINGS']['MANAGERS'] = $GLOBALS['SETTINGS']['ADMINS'];

# Default content type and charset to use for all HttpResponse objects, if a
# MIME type isn't manually specified. These are used to construct the
# Content-Type header.
$GLOBALS['SETTINGS']['DEFAULT_CONTENT_TYPE'] = 'text/html';
$GLOBALS['SETTINGS']['DEFAULT_CHARSET'] = 'utf-8';

# Encoding of files read from disk (template and initial SQL files).
$GLOBALS['SETTINGS']['FILE_CHARSET'] = 'utf-8';

# E-mail address that error messages come from.
$GLOBALS['SETTINGS']['SERVER_EMAIL'] = 'root@localhost';

# Whether to send broken-link e-mails.
$GLOBALS['SETTINGS']['SEND_BROKEN_LINK_EMAILS'] = false;

# New format
$GLOBALS['SETTINGS']['DATABASES'] = array();


# The email backend to use. For possible shortcuts see django.core.mail.
# The default is to use the SMTP backend.
# Third-party backends can be specified by providing a Python path
# to a module that defines an EmailBackend class.
$GLOBALS['SETTINGS']['EMAIL_BACKEND'] = 'django.core.mail.backends.smtp.EmailBackend';

# Host for sending e-mail.
$GLOBALS['SETTINGS']['EMAIL_HOST'] = 'localhost';

# Port for sending e-mail.
$GLOBALS['SETTINGS']['EMAIL_PORT'] = 25;

# Optional SMTP authentication information for EMAIL_HOST.
$GLOBALS['SETTINGS']['EMAIL_HOST_USER'] = '';
$GLOBALS['SETTINGS']['EMAIL_HOST_PASSWORD'] = '';
$GLOBALS['SETTINGS']['EMAIL_USE_TLS'] = false;

# List of strings representing installed apps.
$GLOBALS['SETTINGS']['INSTALLED_APPS'] = array();

# List of locations of the template source files, in search order.
$GLOBALS['SETTINGS']['TEMPLATE_DIRS'] = array();

# List of callables that know how to import templates from various sources.
# See the comments in django/core/template/loader.py for interface
# documentation.
$GLOBALS['SETTINGS']['TEMPLATE_LOADERS'] = array(
    'django.template.loaders.filesystem.Loader',
    'django.template.loaders.app_directories.Loader',
#     'django.template.loaders.eggs.Loader',
);

# List of processors used by RequestContext to populate the context.
# Each one should be a callable that takes the request object as its
# only parameter and returns a dictionary to add to the context.
$GLOBALS['SETTINGS']['TEMPLATE_CONTEXT_PROCESSORS'] = array(
    'django.contrib.auth.context_processors.auth',
    'django.core.context_processors.debug',
    'django.core.context_processors.i18n',
    'django.core.context_processors.media',
#    'django.core.context_processors.request',
    'django.contrib.messages.context_processors.messages',
);

# Output to use in template system for invalid (e.g. misspelled) variables.
$GLOBALS['SETTINGS']['TEMPLATE_STRING_IF_INVALID'] = '';

# URL prefix for admin media -- CSS, JavaScript and images. Make sure to use a
# trailing slash.
# Examples: "http://foo.com/media/", "/media/".
$GLOBALS['SETTINGS']['ADMIN_MEDIA_PREFIX'] = '/media/';

# Default e-mail address to use for various automated correspondence from
# the site managers.
$GLOBALS['SETTINGS']['DEFAULT_FROM_EMAIL'] = 'webmaster@localhost';

# Subject-line prefix for email messages send with django.core.mail.mail_admins
# or ...mail_managers.  Make sure to include the trailing space.
$GLOBALS['SETTINGS']['EMAIL_SUBJECT_PREFIX'] = '[Pjango] ';

# Whether to append trailing slashes to URLs.
$GLOBALS['SETTINGS']['APPEND_SLASH'] = True;

# Whether to prepend the "www." subdomain to URLs that don't have it.
$GLOBALS['SETTINGS']['PREPEND_WWW'] = False;

# Override the server-derived value of SCRIPT_NAME
$GLOBALS['SETTINGS']['FORCE_SCRIPT_NAME'] = false;

# List of compiled regular expression objects representing User-Agent strings
# that are not allowed to visit any page, systemwide. Use this for bad
# robots/crawlers. Here are a few examples:
#     import re
#     DISALLOWED_USER_AGENTS = (
#         re.compile(r'^NaverBot.*'),
#         re.compile(r'^EmailSiphon.*'),
#         re.compile(r'^SiteSucker.*'),
#         re.compile(r'^sohu-search')
#     )
$GLOBALS['SETTINGS']['DISALLOWED_USER_AGENTS'] = array();

$GLOBALS['SETTINGS']['ABSOLUTE_URL_OVERRIDES'] = array();

# Tuple of strings representing allowed prefixes for the {% ssi %} tag.
# Example: ('/home/html', '/var/www')
$GLOBALS['SETTINGS']['ALLOWED_INCLUDE_ROOTS'] = array();

# If this is a admin settings module, this should be a list of
# settings modules (in the format 'foo.bar.baz') for which this admin
# is an admin.
$GLOBALS['SETTINGS']['ADMIN_FOR'] = array();

# 404s that may be ignored.
$GLOBALS['SETTINGS']['IGNORABLE_404_STARTS'] = array('/cgi-bin/', '/_vti_bin', '/_vti_inf');
$GLOBALS['SETTINGS']['IGNORABLE_404_ENDS'] = array('mail.pl', 'mailform.pl', 'mail.cgi', 'mailform.cgi', 'favicon.ico', '.php');

# A secret key for this particular Django installation. Used in secret-key
# hashing algorithms. Set this in your settings, or Django will complain
# loudly.
$GLOBALS['SETTINGS']['SECRET_KEY'] = '';

# Default file storage mechanism that holds media.
$GLOBALS['SETTINGS']['DEFAULT_FILE_STORAGE'] = 'django.core.files.storage.FileSystemStorage';

# Absolute path to the directory that holds media.
# Example: "/home/media/media.lawrence.com/"
$GLOBALS['SETTINGS']['MEDIA_ROOT'] = '';

# URL that handles the media served from MEDIA_ROOT.
# Example: "http://media.lawrence.com"
$GLOBALS['SETTINGS']['MEDIA_URL'] = '';

# List of upload handler classes to be applied in order.
$GLOBALS['SETTINGS']['FILE_UPLOAD_HANDLERS'] = array(
    'django.core.files.uploadhandler.MemoryFileUploadHandler',
    'django.core.files.uploadhandler.TemporaryFileUploadHandler',
);

# Maximum size, in bytes, of a request before it will be streamed to the
# file system instead of into memory.
$GLOBALS['SETTINGS']['FILE_UPLOAD_MAX_MEMORY_SIZE'] = 2621440; # i.e. 2.5 MB

# Directory in which upload streamed files will be temporarily saved. A value of
# `None` will make Django use the operating system's default temporary directory
# (i.e. "/tmp" on *nix systems).
$GLOBALS['SETTINGS']['FILE_UPLOAD_TEMP_DIR'] = false;

# The numeric mode to set newly-uploaded files to. The value should be a mode
# you'd pass directly to os.chmod; see http://docs.python.org/lib/os-file-dir.html.
$GLOBALS['SETTINGS']['FILE_UPLOAD_PERMISSIONS'] = false;

# Python module path where user will place custom format definition.
# The directory where this setting is pointing should contain subdirectories
# named as the locales, containing a formats.py file
# (i.e. "myproject.locale" for myproject/locale/en/formats.py etc. use)
$GLOBALS['SETTINGS']['FORMAT_MODULE_PATH'] = false;

# Default formatting for date objects. See all available format strings here:
# http://docs.djangoproject.com/en/dev/ref/templates/builtins/#now
$GLOBALS['SETTINGS']['DATE_FORMAT'] = 'N j, Y';

# Default formatting for datetime objects. See all available format strings here:
# http://docs.djangoproject.com/en/dev/ref/templates/builtins/#now
$GLOBALS['SETTINGS']['DATETIME_FORMAT'] = 'N j, Y, P';

# Default formatting for time objects. See all available format strings here:
# http://docs.djangoproject.com/en/dev/ref/templates/builtins/#now
$GLOBALS['SETTINGS']['TIME_FORMAT'] = 'P';

# Default formatting for date objects when only the year and month are relevant.
# See all available format strings here:
# http://docs.djangoproject.com/en/dev/ref/templates/builtins/#now
$GLOBALS['SETTINGS']['YEAR_MONTH_FORMAT'] = 'F Y';

# Default formatting for date objects when only the month and day are relevant.
# See all available format strings here:
# http://docs.djangoproject.com/en/dev/ref/templates/builtins/#now
$GLOBALS['SETTINGS']['MONTH_DAY_FORMAT'] = 'F j';

# Default short formatting for date objects. See all available format strings here:
# http://docs.djangoproject.com/en/dev/ref/templates/builtins/#now
$GLOBALS['SETTINGS']['SHORT_DATE_FORMAT'] = 'm/d/Y';

# Default short formatting for datetime objects.
# See all available format strings here:
# http://docs.djangoproject.com/en/dev/ref/templates/builtins/#now
$GLOBALS['SETTINGS']['SHORT_DATETIME_FORMAT'] = 'm/d/Y P';

# Default formats to be used when parsing dates from input boxes, in order
# See all available format string here:
# http://docs.python.org/library/datetime.html#strftime-behavior
# * Note that these format strings are different from the ones to display dates
$GLOBALS['SETTINGS']['DATE_INPUT_FORMATS'] = array(
    '%Y-%m-%d', '%m/%d/%Y', '%m/%d/%y', # '2006-10-25', '10/25/2006', '10/25/06'
    '%b %d %Y', '%b %d, %Y',            # 'Oct 25 2006', 'Oct 25, 2006'
    '%d %b %Y', '%d %b, %Y',            # '25 Oct 2006', '25 Oct, 2006'
    '%B %d %Y', '%B %d, %Y',            # 'October 25 2006', 'October 25, 2006'
    '%d %B %Y', '%d %B, %Y',            # '25 October 2006', '25 October, 2006'
);

# Default formats to be used when parsing times from input boxes, in order
# See all available format string here:
# http://docs.python.org/library/datetime.html#strftime-behavior
# * Note that these format strings are different from the ones to display dates
$GLOBALS['SETTINGS']['TIME_INPUT_FORMATS'] = array(
    '%H:%M:%S',     # '14:30:59'
    '%H:%M',        # '14:30'
);

# Default formats to be used when parsing dates and times from input boxes,
# in order
# See all available format string here:
# http://docs.python.org/library/datetime.html#strftime-behavior
# * Note that these format strings are different from the ones to display dates
$GLOBALS['SETTINGS']['DATETIME_INPUT_FORMATS'] = array(
    '%Y-%m-%d %H:%M:%S',     # '2006-10-25 14:30:59'
    '%Y-%m-%d %H:%M',        # '2006-10-25 14:30'
    '%Y-%m-%d',              # '2006-10-25'
    '%m/%d/%Y %H:%M:%S',     # '10/25/2006 14:30:59'
    '%m/%d/%Y %H:%M',        # '10/25/2006 14:30'
    '%m/%d/%Y',              # '10/25/2006'
    '%m/%d/%y %H:%M:%S',     # '10/25/06 14:30:59'
    '%m/%d/%y %H:%M',        # '10/25/06 14:30'
    '%m/%d/%y',              # '10/25/06'
);

# First day of week, to be used on calendars
# 0 means Sunday, 1 means Monday...
$GLOBALS['SETTINGS']['FIRST_DAY_OF_WEEK'] = 0;

# Decimal separator symbol
$GLOBALS['SETTINGS']['DECIMAL_SEPARATOR'] = '.';

# Boolean that sets whether to add thousand separator when formatting numbers
$GLOBALS['SETTINGS']['USE_THOUSAND_SEPARATOR'] = False;

# Number of digits that will be togheter, when spliting them by THOUSAND_SEPARATOR
# 0 means no grouping, 3 means splitting by thousands...
$GLOBALS['SETTINGS']['NUMBER_GROUPING'] = 0;

# Thousand separator symbol
$GLOBALS['SETTINGS']['THOUSAND_SEPARATOR'] = ',';

# Do you want to manage transactions manually?
# Hint: you really don't!
$GLOBALS['SETTINGS']['TRANSACTIONS_MANAGED'] = False;

# The User-Agent string to use when checking for URL validity through the
# isExistingURL validator.
#from django import get_version
$GLOBALS['SETTINGS']['URL_VALIDATOR_USER_AGENT'] = "Pjango (http://www.pjangoproject.com)";

# The tablespaces to use for each model when not specified otherwise.
$GLOBALS['SETTINGS']['DEFAULT_TABLESPACE'] = '';
$GLOBALS['SETTINGS']['DEFAULT_INDEX_TABLESPACE'] = '';

##############
# MIDDLEWARE #
##############

# List of middleware classes to use.  Order is important; in the request phase,
# this middleware classes will be applied in the order given, and in the
# response phase the middleware will be applied in reverse order.
$GLOBALS['SETTINGS']['MIDDLEWARE_CLASSES'] = array(
    'django.middleware.common.CommonMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
#     'django.middleware.http.ConditionalGetMiddleware',
#     'django.middleware.gzip.GZipMiddleware',
);

############
# SESSIONS #
############

$GLOBALS['SETTINGS']['SESSION_COOKIE_NAME'] = 'sessionid';                    # Cookie name. This can be whatever you want.
$GLOBALS['SETTINGS']['SESSION_COOKIE_AGE'] = 60 * 60 * 24 * 7 * 2;               # Age of cookie, in seconds (default: 2 weeks).
$GLOBALS['SETTINGS']['SESSION_COOKIE_DOMAIN'] = false;                            # A string like ".lawrence.com", or None for standard domain cookie.
$GLOBALS['SETTINGS']['SESSION_COOKIE_SECURE'] = False;                           # Whether the session cookie should be secure (https:// only).
$GLOBALS['SETTINGS']['SESSION_COOKIE_PATH'] = '/';                               # The path of the session cookie.
$GLOBALS['SETTINGS']['SESSION_SAVE_EVERY_REQUEST'] = False;                      # Whether to save the session data on every request.
$GLOBALS['SETTINGS']['SESSION_EXPIRE_AT_BROWSER_CLOSE'] = False;                 # Whether a user's session cookie expires when the Web browser is closed.
$GLOBALS['SETTINGS']['SESSION_ENGINE'] = 'django.contrib.sessions.backends.db';  # The module to store session data
$GLOBALS['SETTINGS']['SESSION_FILE_PATH'] = false;                                # Directory to store session files if using the file session module. If None, the backend will use a sensible default.

#########
# CACHE #
#########

# The cache backend to use.  See the docstring in django.core.cache for the
# possible values.
$GLOBALS['SETTINGS']['CACHE_BACKEND'] = 'locmem://';
$GLOBALS['SETTINGS']['CACHE_MIDDLEWARE_KEY_PREFIX'] = '';
$GLOBALS['SETTINGS']['CACHE_MIDDLEWARE_SECONDS'] = 600;

####################
# COMMENTS         #
####################

$GLOBALS['SETTINGS']['COMMENTS_ALLOW_PROFANITIES'] = False;

# The profanities that will trigger a validation error in the
# 'hasNoProfanities' validator. All of these should be in lowercase.
$GLOBALS['SETTINGS']['PROFANITIES_LIST'] = array('asshat', 'asshead', 'asshole', 'cunt', 'fuck', 'gook', 'nigger', 'shit');

# The group ID that designates which users are banned.
# Set to None if you're not using it.
$GLOBALS['SETTINGS']['COMMENTS_BANNED_USERS_GROUP'] = false;

# The group ID that designates which users can moderate comments.
# Set to None if you're not using it.
$GLOBALS['SETTINGS']['COMMENTS_MODERATORS_GROUP'] = false;

# The group ID that designates the users whose comments should be e-mailed to MANAGERS.
# Set to None if you're not using it.
$GLOBALS['SETTINGS']['COMMENTS_SKETCHY_USERS_GROUP'] = false;

# The system will e-mail MANAGERS the first COMMENTS_FIRST_FEW comments by each
# user. Set this to 0 if you want to disable it.
$GLOBALS['SETTINGS']['COMMENTS_FIRST_FEW'] = 0;

# A tuple of IP addresses that have been banned from participating in various
# Django-powered features.
$GLOBALS['SETTINGS']['BANNED_IPS'] = array();

##################
# AUTHENTICATION #
##################

$GLOBALS['SETTINGS']['AUTHENTICATION_BACKENDS'] = array('django.contrib.auth.backends.ModelBackend');

$GLOBALS['SETTINGS']['LOGIN_URL'] = '/auth/login/';

$GLOBALS['SETTINGS']['LOGOUT_URL'] = '/auth/logout/';

$GLOBALS['SETTINGS']['LOGIN_REDIRECT_URL'] = '/accounts/profile/';

# The number of days a password reset link is valid for
$GLOBALS['SETTINGS']['PASSWORD_RESET_TIMEOUT_DAYS'] = 3;

########
# CSRF #
########

# Dotted path to callable to be used as view when a request is
# rejected by the CSRF middleware.
$GLOBALS['SETTINGS']['CSRF_FAILURE_VIEW'] = 'django.views.csrf.csrf_failure';

# Name and domain for CSRF cookie.
$GLOBALS['SETTINGS']['CSRF_COOKIE_NAME'] = 'csrftoken';
$GLOBALS['SETTINGS']['CSRF_COOKIE_DOMAIN'] = false;

############
# MESSAGES #
############

# Class to use as messges backend
$GLOBALS['SETTINGS']['MESSAGE_STORAGE'] = 'django.contrib.messages.storage.user_messages.LegacyFallbackStorage';

# Default values of MESSAGE_LEVEL and MESSAGE_TAGS are defined within
# django.contrib.messages to avoid imports in this settings file.


############
# FIXTURES #
############

# The list of directories to search for fixtures
$GLOBALS['SETTINGS']['FIXTURE_DIRS'] = array();


############
# LOGGING #
############
$GLOBALS['SETTINGS']['LOGGING'] = array(
	'handler'        => 'file',
	'ident'          => 'ident',	
    'name'           => sprintf('%s/log/out_%s.log', SITE_PATH, date('d-m-Y')),
// 	'level'          => PEAR_LOG_DEBUG,
	'mode'           => 0600, 
	'timeFormat'     => '%X %x'
);
