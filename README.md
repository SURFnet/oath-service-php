oath-service-php
================

PHP implementation of an OATH service


Setting up YubiHSM
==================

# Preparing YubiHSM

Press the small configuration button using a paperclip or a pin on YubiHSM and insert it into a USB slot of a trusted computer. This puts the
HSM into configuration mode and it can be programmed.

Next connect to the HSM using _minicom_ program. On Debian you can find it in apt-get and on Mac OS X homebrew contains it. The default device
node on Debian is /dev/ttyACM0 and on OS X /dev/tty.usbmodem1411.

    minicom -D /dev/ttyACM0

Next execute the following commands to initialise the HSM:

```
HSM> zap                                                                                                                                                                                    
Confirm current config being erased (type yes) yes - wait - done                                                                                                                            
NO_CFG> hsm                                                                                                                                                                                 
Enabled flags 7fffffff = YSM_AEAD_GENERATE,YSM_BUFFER_AEAD_GENERATE,YSM_RANDOM_AEAD_GENERATE,YSM_AEAD_DECRYPT_CMP,YSM_DB_YUBIKEY_AEAD_STORE,YSM_AEAD_YUBIKEY_OTP_DECODE,YSM_DB_YUBIKEY_OTP_VALIDATE,YSM_AES_ECB_BLOCK_ENCRYPT,YSM_AES_ECB_BLOCK_DECRYPT,YSM_AES_ECB_BLOCK_DECRYPT_CMP,YSM_HMAC_SHA1_GENERATE,YSM_TEMD
                                                                                                                                                                                            
a:YSM_AEAD_GENERATE           b:YSM_BUFFER_AEAD_GENERATE                                                                                                                                    
c:YSM_RANDOM_AEAD_GENERATE    d:YSM_AEAD_DECRYPT_CMP                                                                                                                                        
e:YSM_DB_YUBIKEY_AEAD_STORE   f:YSM_AEAD_YUBIKEY_OTP_DECODE                                                                                                                                 
g:YSM_DB_YUBIKEY_OTP_VALIDATE h:YSM_AES_ECB_BLOCK_ENCRYPT   
i:YSM_AES_ECB_BLOCK_DECRYPT   j:YSM_AES_ECB_BLOCK_DECRYPT_CMP
k:YSM_HMAC_SHA1_GENERATE      l:YSM_TEMP_KEY_LOAD           
m:YSM_USER_NONCE              n:YSM_BUFFER_LOAD             
o:FLAG_DEBUG                  

Toggle bit (space = all, enter = exit) 
Enter cfg password (g to generate) <cfg password will appear here if you press g> 
Enter admin Yubikey public id 001/008 (enter when done) ccccccdtvlcb
Enter admin Yubikey public id 002/008 (enter when done) 
Enter master key (g to generate) <master key will appear here if you press g> 
Confirm current config being erased (type yes) yes - wait - done
HSM (keys changed)> keycommit - Done
HSM> dbload - Load id data now. Press ESC to quit
00001 - inserted ok
HSM> keycommit - Done
```

Note: Make sure you store cfg password and master key into a safe place


In the step where you give dbload command you need to copy and paste YubiKey information in the following format:

    <index>,<public id><private id>,<AES key>,,,

Being puzzled for a while and trying to find these values finally I encoutered "YubiKey Personalization Tool". The values
are available in this tool and can be changed or copied from there.

So in my case the line for YubiKey NEO looked a bit like this (I changed random numbers and letters for the sake of it):

    00001,cdecccdtawks,7202ef37d79e,de3d87cc221e5c5ec30d270fed3bf023,,,

So, now your HSM is initialised with a single UbiKey and it's ready to do validation of OTPs. You can verify that everything went well by
generating an OTP with the YubiKey configured in dbload step and using _otpver_ command in minicom. 

# Generating Keys

Next step is to generate a key to use to encrypt the secrets. Key can be generated using the following command in minicom:

keygen <start index> <number of keys to generate> <length>

The following command will create keyhandle 98 with a 20 byte key:

    keygen 98 1 20

You should make a note of the generated key in case you lose access to the YubiHSM for some reason. Just keep it safe. At this point
it's good to make note of the key handle as well because this handle will be used to generate AEADs for user secrets later.


# Server-side preparations

python-pyhsm and lockfile is required on the server-side. The package is available in apt:

    apt-get install python-pyhsm
    apt-get install python-lockfile

or using pip:

    pip install pyhsm
    pip install lockfile

If YubiHSM is unplugged from the server the keystore needs to be unlocked using the following command:

    yhsm-keystore-unlock --device /dev/ttyACM0





In order for the web-server user to validate the OTP tokens it must be added to the group that owns the device node for YubiHSM or an additional
udev rule is needed to change the group of the device node. For example:

    /etc/udev/rules.d/99-yubihsm.rules:
    SUBSYSTEM=="usb", ATTRS{idVendor}=="1050", ATTRS{idProduct}=="0018", GROUP="www-data"


