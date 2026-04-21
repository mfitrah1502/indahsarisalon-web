const fs = require('fs');
const content = fs.readFileSync('C:/laragon/www/indahsarisalon-web/resources/views/booking/select.blade.php', 'utf8');
const match = content.match(/<script>([\s\S]*?)<\/script>/);
if (match) {
    let script = match[1];
    // Mock the blade variables
    script = script.replace(/@json\(\$stylists\)/g, '[]');
    script = script.replace(/@foreach.*?@endforeach/gs, '');
    script = script.replace(/@if.*?@endif/gs, '');
    script = script.replace(/{{.+?}}/g, '1');
    script = script.replace(/{!!.+?!!}/g, '1');
    fs.writeFileSync('C:/laragon/www/indahsarisalon-web/test_script.js', script);
}
