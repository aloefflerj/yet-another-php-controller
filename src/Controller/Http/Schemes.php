<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

trait Schemes
{
    public function getValidSchemes(): array
    {
        return [
            'aaa',
            'aaas',
            'about',
            'acap',
            'acct',
            'acd',
            'acr',
            'adiumxtra',
            'adt',
            'afp',
            'afs',
            'aim',
            'amss',
            'android',
            'appdata',
            'apt',
            'ar',
            'ark',
            'attachment',
            'aw',
            'barion',
            'beshare',
            'bitcoin',
            'bitcoincash',
            'blob',
            'bolo',
            'browserext',
            'cabal',
            'calculator',
            'callto',
            'cap',
            'cast',
            'casts',
            'chrome',
            'chrome-extension',
            'cid',
            'coap',
            'coap+tcp',
            'coap+ws',
            'coaps',
            'coaps+tcp',
            'coaps+ws',
            'com-eventbrite-attendee',
            'content',
            'content-type',
            'crid',
            'cvs',
            'dab',
            'dat',
            'data',
            'dav',
            'diaspora',
            'dict',
            'did',
            'dis',
            'dlna-playcontainer',
            'dlna-playsingle',
            'dns',
            'dntp',
            'doi',
            'dpp',
            'drm',
            'drop',
            'dtmi',
            'dtn',
            'dvb',
            'dvx',
            'dweb',
            'ed2k',
            'elsi',
            'embedded',
            'ens',
            'ethereum',
            'example',
            'facetime',
            'fax',
            'feed',
            'feedready',
            'fido',
            'file',
            'filesystem',
            'finger',
            'first-run-pen-experience',
            'fish',
            'fm',
            'ftp',
            'fuchsia-pkg',
            'geo',
            'gg',
            'git',
            'gizmoproject',
            'go',
            'gopher',
            'graph',
            'gtalk',
            'h323',
            'ham',
            'hcap',
            'hcp',
            'http',
            'https',
            'hxxp',
            'hxxps',
            'hydrazone',
            'hyper',
            'iax',
            'icap',
            'icon',
            'im',
            'imap',
            'info',
            'iotdisco',
            'ipfs',
            'ipn',
            'ipns',
            'ipp',
            'ipps',
            'irc',
            'irc6',
            'ircs',
            'iris',
            'iris.beep',
            'iris.lwz',
            'iris.xpc',
            'iris.xpcs',
            'isostore',
            'itms',
            'jabber',
            'jar',
            'jms',
            'keyparc',
            'lastfm',
            'lbry',
            'ldap',
            'ldaps',
            'leaptofrogans',
            'lorawan',
            'lvlt',
            'magnet',
            'mailserver',
            'mailto',
            'maps',
            'market',
            'matrix',
            'message',
            'microsoft.windows.camera',
            'microsoft.windows.camera.multipicker',
            'microsoft.windows.camera.picker',
            'mid',
            'mms',
            'modem',
            'mongodb',
            'moz',
            'ms-access',
            'ms-browser-extension',
            'ms-calculator',
            'ms-drive-to',
            'ms-enrollment',
            'ms-excel',
            'ms-eyecontrolspeech',
            'ms-gamebarservices',
            'ms-gamingoverlay',
            'ms-getoffice',
            'ms-help',
            'ms-infopath',
            'ms-inputapp',
            'ms-lockscreencomponent-config',
            'ms-media-stream-id',
            'ms-meetnow',
            'ms-mixedrealitycapture',
            'ms-mobileplans',
            'ms-officeapp',
            'ms-people',
            'ms-project',
            'ms-powerpoint',
            'ms-publisher',
            'ms-restoretabcompanion',
            'ms-screenclip',
            'ms-screensketch',
            'ms-search',
            'ms-search-repair',
            'ms-secondary-screen-controller',
            'ms-secondary-screen-setup',
            'ms-settings',
            'ms-settings-airplanemode',
            'ms-settings-bluetooth',
            'ms-settings-camera',
            'ms-settings-cellular',
            'ms-settings-cloudstorage',
            'ms-settings-connectabledevices,',
            'ms-settings-displays-topology',
            'ms-settings-emailandaccounts',
            'ms-settings-language',
            'ms-settings-location',
            'ms-settings-lock',
            'ms-settings-nfctransactions',
            'ms-settings-notifications',
            'ms-settings-power',
            'ms-settings-privacy',
            'ms-settings-proximity',
            'ms-settings-screenrotation',
            'ms-settings-wifi',
            'ms-settings-workplace',
            'ms-spd',
            'ms-stickers',
            'ms-sttoverlay',
            'ms-transit-to',
            'ms-useractivityset',
            'ms-virtualtouchpad',
            'ms-visio',
            'ms-walk-to',
            'ms-whiteboard',
            'ms-whiteboard-cmd',
            'ms-word',
            'msnim',
            'msrp',
            'msrps',
            'mss',
            'mt',
            'mtqp',
            'mumble',
            'mupdate',
            'mvn',
            'news',
            'nfs',
            'ni',
            'nih',
            'nntp',
            'notes',
            'num',
            'ocf',
            'oid',
            'onenote',
            'onenote-cmd',
            'opaquelocktoken',
            'openpgp4fpr',
            'otpauth',
            'pack',
            'palm',
            'paparazzi',
            'payment',
            'payto',
            'pkcs11',
            'platform',
            'pop',
            'pres',
            'prospero',
            'proxy',
            'pwid',
            'psyc',
            'pttp',
            'qb',
            'query',
            'quic-transport,',
            'redis',
            'rediss',
            'reload',
            'res',
            'resource',
            'rmi',
            'rsync',
            'rtmfp',
            'rtmp',
            'rtsp',
            'rtsps',
            'rtspu',
            'sarif',
            'secondlife',
            'secret-token',
            'service',
            'session',
            'sftp',
            'sgn',
            'shc',
            'shttp',
            'sieve',
            'simpleledger',
            'simplex',
            'sip',
            'sips',
            'skype',
            'smb',
            'smp',
            'sms',
            'smtp',
            'snews',
            'snmp',
            'soap.beep',
            'soap.beeps',
            'soldat',
            'spiffe',
            'spotify',
            'ssb',
            'ssh',
            'steam',
            'stun',
            'stuns',
            'submit',
            'svn',
            'swh',
            'swid',
            'swidpath',
            'tag',
            'teamspeak',
            'tel',
            'teliaeid',
            'telnet',
            'tftp',
            'things',
            'thismessage',
            'tip',
            'tn3270',
            'tool',
            'turn',
            'turns',
            'tv',
            'udp',
            'unreal',
            'urn',
            'ut2004',
            'uuid-in-package',
            'v-event',
            'vemmi',
            'ventrilo',
            'ves',
            'videotex',
            'vnc',
            'view-source',
            'vscode',
            'vscode-insiders',
            'vsls',
            'wais',
            'wcr',
            'webcal',
            'wifi',
            'wpid',
            'ws',
            'wss',
            'wtai',
            'wyciwyg',
            'xcon',
            'xcon-userid',
            'xfire',
            'xmlrpc.beep',
            'xmlrpc.beeps',
            'xmpp',
            'xri',
            'ymsgr',
            'z39.50',
            'z39.50r',
            'z39.50s'
        ];
    }
}
