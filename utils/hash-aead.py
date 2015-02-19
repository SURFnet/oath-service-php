#!/usr/bin/env python
#
# Generate SHA1 HMAC using a keyhandle
#
# Based on python-pyhsm tools. See COPYING.pyhsm for more details
#

import sys
import struct
import argparse
import pyhsm
import pyhsm.oath_hotp

from lockfile import locked

default_device = "/dev/ttyACM0"

def parse_args():
    """
    Parse the command line arguments
    """
    global default_device

    parser = argparse.ArgumentParser(description = 'Generate SHA1 HMAC using a keyhandle',
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
                        help='Key handle',
                        metavar='HANDLE',
                        )
    parser.add_argument('--aead',
                        dest='aead',
                        required=True,
                        help='The aead hex encoded',
                        metavar='AEAD',
                        )
    parser.add_argument('--nonce',
                        dest='nonce',
                        required=True,
                        help='The nonce hex encoded',
                        metavar='NONCE',
                        )
    parser.add_argument('--data',
                        dest='data',
                        required=True,
                        help='The data to hash as hex encoded string',
                        metavar='STR',
                        )
    args = parser.parse_args()
    return args

def args_fixup(args):
    args.key_handle = pyhsm.util.key_handle_to_int(args.key_handle)

@locked("/tmp/oath-hsm-serial.lock")
def main():
    args = parse_args()
    args_fixup(args)

    hsm = pyhsm.YHSM(device = args.device, debug=args.debug)

    # Load our key
    nonce = args.nonce.decode('hex')
    aead  = args.aead.decode('hex')
    data  = args.data.decode('hex')

    hsm.load_temp_key(nonce, args.key_handle, aead)
    print hsm.hmac_sha1(pyhsm.defines.YSM_TEMP_KEY_HANDLE, data).get_hash().encode('hex')

    return True


if __name__ == '__main__':
    if main():
        sys.exit(0)
    sys.exit(1)
