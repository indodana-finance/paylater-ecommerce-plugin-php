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

  refreshTimestamp = currentTimestamp + credentials.lease_duration * 1000;
  console.log("Next Credential Refresh: " + new Date(refreshTimestamp));

  setTimeout(function() {
    const currentTimestampForNextRefresh = Date.now();
    refreshCredential(currentTimestampForNextRefresh);
  }, refreshTimestamp - currentTimestamp);
}

const currentTimestamp = Date.now();
refreshCredential(currentTimestamp);
