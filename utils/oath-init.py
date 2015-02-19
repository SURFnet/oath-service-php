#!/usr/bin/env python
#
# Initialise OATH AEAD and nonce
#
# Based on python-pyhsm tools. See COPYING.pyhsm for more details
#

import sys
import struct
import argparse
import pyhsm
import pyhsm.oath_hotp
import json

from lockfile import locked

default_device = "/dev/ttyACM0"

def parse_args():
    """
    Parse the command line arguments
    """
    global default_device

    parser = argparse.ArgumentParser(description = 'Initialize OATH token',
                                     add_help=True,
                                     formatter_class=argparse.ArgumentDefaultsHelpFormatter,
                                     )
    parser.add_argument('-D', '--device',
                        dest='device',
                        default=default_device,
                        required=False,
                        help='YubiHSM device',
                        )
    parser.add_argument('--debug',
                        dest='debug',
                        action='store_true', default=False,
                        help='Enable debug operation',
                        )

    parser.add_argument('--key-handle',
                        dest='key_handle',
                        required=True,
                        help='Key handle to create AEAD',
                        metavar='HANDLE',
                        )
    parser.add_argument('--oath-k',
                        dest='oath_k',
                        required=False,
                        help='The secret key of the token, hex encoded',
                        metavar='HEXSTR',
                        )

    args = parser.parse_args()
    return args

def args_fixup(args):
    keyhandles_fixup(args)

def keyhandles_fixup(args):
    args.key_handle = pyhsm.util.key_handle_to_int(args.key_handle)

def generate_aead(hsm, args):
    """ Protect the oath-k in an AEAD. """
    key = get_oath_k(args)
    # Enabled flags 00010000 = YSM_HMAC_SHA1_GENERATE
    flags = struct.pack("< I", 0x10000)
    hsm.load_secret(key + flags)
    nonce = hsm.get_nonce().nonce
    aead = hsm.generate_aead(nonce, args.key_handle)
    if args.debug:
        print "AEAD: %s (%s)" % (aead.data.encode('hex'), aead)
    return nonce, aead

def get_oath_k(args):
    """ Get the OATH K value (secret key), either from args or by prompting. """
    if args.oath_k:
        decoded = args.oath_k.decode('hex')
    else:
        t = raw_input("Enter OATH key (hex encoded) : ")
        decoded = t.decode('hex')
    return decoded

def display_oath_entry(args, nonce, aead):
    """ Display the AEAD. """
    data = {"aead": aead.data.encode('hex'),
            "nonce": nonce.encode('hex'),
            "key_handle": args.key_handle,
            }

    print json.dumps(data)
    return True

@locked("/tmp/oath-hsm-serial.lock")
def main():
    args = parse_args()
    args_fixup(args)

    hsm = pyhsm.YHSM(device = args.device, debug=args.debug)

    nonce, aead = generate_aead(hsm, args)
    return display_oath_entry(args, nonce, aead)

if __name__ == '__main__':
    if main():
        sys.exit(0)
    sys.exit(1)
