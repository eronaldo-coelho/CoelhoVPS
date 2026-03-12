import requests
import mysql.connector
import base64
from datetime import datetime, timedelta
import os

MAILJET_KEY = "2752a5c7461dc63025c00aa0f7839d51"
MAILJET_SECRET = "a050c82964517dae53f5bb49725f930b"
MAILJET_URL = "https://api.mailjet.com/v3.1/send"

db = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="coelhovps",
    database="coelhovps"
)

cursor = db.cursor(dictionary=True)

cursor.execute("""
CREATE TABLE IF NOT EXISTS email_logs (
    user_id INT PRIMARY KEY,
    ultimo_envio DATETIME NOT NULL
)
""")

cursor.execute("""
SELECT u.id, u.name, u.email, l.ultimo_envio
FROM users u
LEFT JOIN contratos c ON c.user_id = u.id
LEFT JOIN email_logs l ON l.user_id = u.id
WHERE c.id IS NULL
""")

usuarios = cursor.fetchall()

logo_path = "public/logo.png"
logo_base64 = base64.b64encode(open(logo_path, "rb").read()).decode()

def pode_enviar(ultimo_envio):
    if not ultimo_envio:
        return True
    return datetime.now() - ultimo_envio >= timedelta(days=7)

def enviar_email(dest_email, dest_nome):
    html = f"""
    <div style="font-family: Arial, sans-serif;">
        <img src="https://coelhovps.com.br/logo.png" style="max-width:200px;margin-bottom:20px;">
        <p>Olá <strong>{dest_nome}</strong>,</p>
        <p>Vimos que você se cadastrou na <strong>CoelhoVPS</strong>, mas ainda não contratou nenhum de nossos servidores VPS.</p>
        <p>Talvez tenha ficado alguma dúvida ou faltado alguma informação na hora da escolha.</p>
        <p>Temos VPS com excelente desempenho, SSD/NVMe, tráfego alto e suporte direto comigo.</p>
        <p>Se quiser ajuda, é só chamar no WhatsApp:<br><strong>(13) 98203-8196</strong></p>
        <p>Atenciosamente,<br><strong>CoelhoVPS</strong></p>
    </div>
    """

    payload = {
        "Messages": [
            {
                "From": {
                    "Email": "comercial@coelhovps.com.br",
                    "Name": "CoelhoVPS"
                },
                "To": [
                    {
                        "Email": dest_email,
                        "Name": dest_nome
                    }
                ],
                "Subject": "Você se cadastrou — falta só ativar seu VPS 🚀",
                "HTMLPart": html,
                "InlineAttachments": [
                    {
                        "ContentType": "image/png",
                        "Filename": "logo.png",
                        "ContentID": "logo",
                        "Base64Content": logo_base64
                    }
                ]
            }
        ]
    }

    r = requests.post(
        MAILJET_URL,
        auth=(MAILJET_KEY, MAILJET_SECRET),
        json=payload,
        timeout=10
    )

    return r.status_code == 200, html

def enviar_copia_admin(html, dest_email, dest_nome):
    payload = {
        "Messages": [
            {
                "From": {
                    "Email": "comercial@coelhovps.com.br",
                    "Name": "CoelhoVPS"
                },
                "To": [
                    {
                        "Email": "eronaldocoelho58@gmail.com",
                        "Name": "Eronaldo"
                    }
                ],
                "Subject": f"Cópia de e-mail enviado para {dest_email}",
                "HTMLPart": html,
                "InlineAttachments": [
                    {
                        "ContentType": "image/png",
                        "Filename": "logo.png",
                        "ContentID": "logo",
                        "Base64Content": logo_base64
                    }
                ]
            }
        ]
    }

    requests.post(
        MAILJET_URL,
        auth=(MAILJET_KEY, MAILJET_SECRET),
        json=payload,
        timeout=10
    )

for u in usuarios:
    if pode_enviar(u["ultimo_envio"]):
        sucesso, html = enviar_email(u["email"], u["name"])
        if sucesso:
            enviar_copia_admin(html, u["email"], u["name"])
            cursor.execute("""
            INSERT INTO email_logs (user_id, ultimo_envio)
            VALUES (%s, %s)
            ON DUPLICATE KEY UPDATE ultimo_envio = VALUES(ultimo_envio)
            """, (u["id"], datetime.now()))
            db.commit()

print("Processo finalizado.")