const nunjucks = require("nunjucks");
const yaml = require("js-yaml");
const fs = require("fs");
const execSync = require('child_process').execSync;
const dotenv = require("dotenv");

dotenv.config({
  path: "../../../cli/NAMESPACE"
});

const ORGANIZATION = process.env.ORGANIZATION;
const TEAM = process.env.TEAM;
const PRODUCT = process.env.PRODUCT;
const IDENTIFIER = `hosted.mysql.${ORGANIZATION}.${TEAM}.${PRODUCT}.opencartv1.dev`;
const DBCTL_CREDENTIAL_FILE = `../../../cli/.run.db/${IDENTIFIER}/.credentials/app.yaml`
const CONFIG_RELATIVE_PATH = "upload/config.php";
const ADMIN_CONFIG_RELATIVE_PATH = "upload/admin/config.php";

function getCredential(environment) {
  try {
    execSync(`./helper.sh ${environment}`, { stdio: "inherit", shell: "/bin/bash", env: process.env });
  } catch(error) {
    console.log(error);
  }
}

function readCredential() {
  getCredential(process.env.ENVIRONMENT);
  const credentials = yaml.safeLoad(
    fs.readFileSync(DBCTL_CREDENTIAL_FILE, "utf8")
  );
  return credentials;
}

function writeCredentialToTemplate(username, password, inputPath, outputPath) {
  nunjucks.configure({ autoescape: true });
  const result = nunjucks.render(inputPath, {
    db_username: username,
    db_password: password
  });
  const outputFd = fs.openSync(outputPath, "w");
  fs.writeSync(outputFd, result);
}

function refreshCredential(currentTimestamp) {
  const credentials = readCredential();
  writeCredentialToTemplate(credentials.data.username, credentials.data.password, "config.njk", CONFIG_RELATIVE_PATH);
  writeCredentialToTemplate(credentials.data.username, credentials.data.password, "admin.config.njk", ADMIN_CONFIG_RELATIVE_PATH);

  // Lease duration was defined in seconds, thus we times it by a thousand to get ms
  return currentTimestamp + credentials.lease_duration * 1000;
}

let refreshTimestamp = 0;
while (true) {
  let currentTimestamp = Date.now();
  // We need to refresh credentials 10 minutes (600000 ms) before it expires
  if (currentTimestamp > refreshTimestamp - 600000) {
    refreshTimestamp = refreshCredential(currentTimestamp);
    console.log("Current timestamp: " + currentTimestamp);
    console.log("Next Credential Refresh: " + refreshTimestamp);
  }
}
