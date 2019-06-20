const nunjucks = require("nunjucks");
const yaml = require("js-yaml");
const fs = require("fs");

function generateNginxConfig() {
    const result = nunjucks.render("opencartv3.conf.njk", {
        build_directory: __dirname + "/upload/"
    });
    const outputFd = fs.openSync("/etc/nginx/conf.d/opencartv3.conf", "w");
    fs.writeSync(outputFd, result);
}

generateNginxConfig();
