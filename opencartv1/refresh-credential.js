const nunjucks = require("nunjucks");
const yaml = require("js-yaml");
const request = require("request-promise");
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

const CACERT_FILE_PATH = "/usr/share/pki/certs/service/pios/pios-stg.ca.crt";
const CERT_FILE_PATH = "/usr/share/pki/certs/service/pios/pios-stg.crt";
const KEY_FILE_PATH = "/usr/share/pki/certs/service/pios/.private/pios-stg.key";

const VAULT_BASE_URL = "https://vault.cermati.com:8443";

function getToken(environment) {
  const role = `${ORGANIZATION}-${TEAM}-${PRODUCT}-${environment}`;
  const options = {
    url: `${VAULT_BASE_URL}/v1/auth/cert/login`,
    cert: fs.readFileSync(CERT_FILE_PATH),
    key: fs.readFileSync(KEY_FILE_PATH),
    ca: fs.readFileSync(CACERT_FILE_PATH),
    form: {
      "name": role
    }
  };
  return request.post(options).then(response => {
    console.log(await getToken(environment));
    return response.data.auth.client_token;
  });
}

async function getCredential(environment) {
  const token = await getToken(environment);
  // try {
  //   execSync(`./helper.sh ${environment}`, { stdio: "inherit", shell: "/bin/bash", env: process.env });
  // } catch(error) {
  //   console.log(error);
  // }
}

async function readCredential() {
  await getCredential(process.env.ENVIRONMENT);
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

async function refreshCredential(currentTimestamp) {
  const credentials = await readCredential();
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
