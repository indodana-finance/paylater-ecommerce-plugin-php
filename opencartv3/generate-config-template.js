const nunjucks = require("nunjucks");
const yaml = require("js-yaml");
const fs = require("fs");

function generateConfig(inputPath, outputPath) {
    const result = nunjucks.render(inputPath, {
        build_directory: __dirname
    });
    const outputFd = fs.openSync(outputPath, "w");
    fs.writeSync(outputFd, result);
}

generateConfig("config.template.njk", "config.njk");
generateConfig("admin.config.template.njk", "admin.config.njk");
