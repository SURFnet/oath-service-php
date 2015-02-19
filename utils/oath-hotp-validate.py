#!/usr/bin/env python
#
# Keyhandle must be configured with:
#   00000012 - YSM_AEAD_GENERATE | YSM_AEAD_DECRYPT_CMP
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

    parser = argparse.ArgumentParser(description = 'Generate AEAD using a keyhandle',
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
                        action='store_true',
                        default=False,
                        help='Enable debug operation',
                        )
    parser.add_argument('--key-handle',
                        dest='key_handle',
                        required=True,
                        help='Key handle',
                        metavar='HANDLE',
                        )
    parser.add_argument('--token',
                        dest='token',
                        required=True,
                        help='The token from user',
                        metavar='TOKEN',
                        )
    parser.add_argument('--counter',
                        dest='counter',
                        required=True,
                        help='The counter from user',
                        metavar='COUNTER',
                        )
    parser.add_argument('--aead',
                        dest='aead',
                        required=True,
                        help='The aead',
                        metavar='AEAD',
                        )
    parser.add_argument('--nonce',
                        dest='nonce',
                        required=True,
                        help='The nonce',
                        metavar='NONCE',
                        )
    parser.add_argument('--look-ahead',
                        dest='look_ahead',
                        required=False,
                        default=10,
                        help='Look ahead window',
                        metavar='LOOKAHEAD',
                        )
    args = parser.parse_args()
    return args

def args_fixup(args):
    args.key_handle = pyhsm.util.key_handle_to_int(args.key_handle)
    args.counter    = int(args.counter)
    args.token      = int(args.token)
    args.look_ahead = int(args.look_ahead)

@locked("/tmp/oath-hsm-serial.lock")
def main():
    args = parse_args()
    args_fixup(args)

    hsm = pyhsm.YHSM(device = args.device, debug=args.debug)

    nonce = args.nonce.decode('hex')
    aead  = args.aead.decode('hex')

    new_counter = pyhsm.oath_hotp.search_for_oath_code(hsm, args.key_handle, nonce, aead, \
                                                        args.counter, args.token, args.look_ahead)

    if new_counter == args.counter + 1:
        print new_counter
        return True

    print "FAIL"
    return False


if __name__ == '__main__':
    if main():
        sys.exit(0)

    sys.exit(1)




