docker run -it --rm \
    -v "/etc/letsencrypt:/etc/letsencrypt" \
    -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
    -v "/var/log/letsencrypt:/var/log/letsencrypt" \
    certbot/certbot certonly --standalone \
    --preferred-challenges http \
    --agree-tos \
    --email your-email@example.com \
    -d yourdomain.com
