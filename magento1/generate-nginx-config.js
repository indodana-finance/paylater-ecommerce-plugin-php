const nunjucks = require("nunjucks");
const yaml = require("js-yaml");
const fs = require("fs");

function generateNginxConfig() {
    const result = nunjucks.render("magento1.conf.njk", {
        build_directory: __dirname + "/upload/",
	host: process.env.HOST,
	port: process.env.PORT
    });
    const outputFd = fs.openSync("/etc/nginx/conf.d/magento1.conf", "w");
    fs.writeSync(outputFd, result);
}

generateNginxConfig();
