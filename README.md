
1. Upload project to server
2. Run `composer install` inside project root
3. Ensure `uploads/` is writable: `chmod -R 755 uploads`
4. API endpoint: `/public/api/contact.php`

```bash
curl -X POST -F "phone=+431234567" -F "email=test@example.com" -F "time_to_call=Tomorrow" -F "file=@/path/to/license.pdf" https://yourdomain.com/api/contact.php
```
