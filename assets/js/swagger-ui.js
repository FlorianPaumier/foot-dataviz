require('../../node_modules/swagger-ui-dist/swagger-ui.css');
const SwaggerUIBundle = require('swagger-ui-dist/swagger-ui-bundle');
const SwaggerUIStandalonePreset = require('swagger-ui-dist/swagger-ui-standalone-preset');

window.onload = function () {
    var url = document.querySelector('#swagger-ui').dataset.url;

    const ui = SwaggerUIBundle({
        url: url,
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "StandaloneLayout",
        filter: true
    });

    window.ui = ui
};