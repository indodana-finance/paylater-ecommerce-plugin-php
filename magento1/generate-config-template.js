const nunjucks = require("nunjucks");
const yaml = require("js-yaml");
const fs = require("fs");

function generateNginxConfig() {
    const result = nunjucks.render("config.template.njk", {
        build_directory: __dirname
    });
    const outputFd = fs.openSync("config.njk", "w");
    fs.writeSync(outputFd, result);
}

generateNginxConfig();
