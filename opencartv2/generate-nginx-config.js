const nunjucks = require("nunjucks");
const yaml = require("js-yaml");
const fs = require("fs");

function generateNginxConfig() {
    const result = nunjucks.render("opencartv2.conf.njk", {
        build_directory: __dirname + "/upload/"
    });
    const outputFd = fs.openSync("/etc/nginx/conf.d/opencartv2.conf", "w");
    fs.writeSync(outputFd, result);
}

generateNginxConfig();
