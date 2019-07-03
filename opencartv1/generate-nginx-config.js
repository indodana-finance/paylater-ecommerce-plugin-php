const nunjucks = require("nunjucks");
const fs = require("fs");

const HOST = process.env.HOST;
const PORT = process.env.PORT;

function generateNginxConfig() {
    const result = nunjucks.render("opencartv1.conf.njk", {
        build_directory: __dirname + "/upload/",
        port: PORT,
        host: HOST
    });
    const outputFd = fs.openSync("/etc/nginx/conf.d/opencartv1.conf", "w");
    fs.writeSync(outputFd, result);
}

generateNginxConfig();
