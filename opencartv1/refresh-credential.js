const nunjucks = require("nunjucks");
const request = require("request-promise");
const fs = require("fs");
const dotenv = require("dotenv");
const yaml = require("js-yaml");

dotenv.config({
  path: "../../../cli/NAMESPACE"
});

const HOST = process.env.HOST;
const PORT = process.env.PORT || "80";

const ORGANIZATION = process.env.ORGANIZATION;
const TEAM = process.env.TEAM;
const PRODUCT = process.env.PRODUCT;
const ENVIRONMENT = process.env.ENVIRONMENT;

const CONFIG_RELATIVE_PATH = "upload/config.php";
const ADMIN_CONFIG_RELATIVE_PATH = "upload/admin/config.php";

const CACERT_FILE_PATH = `/usr/share/pki/certs/service/pios-${ENVIRONMENT}/pios-${ENVIRONMENT}.ca.crt`;
const CERT_FILE_PATH = `/usr/share/pki/certs/service/pios-${ENVIRONMENT}/pios-${ENVIRONMENT}.crt`;
const KEY_FILE_PATH = `/usr/share/pki/certs/service/pios-${ENVIRONMENT}/.private/pios-${ENVIRONMENT}.key`;

function getVaultBaseUrl() {
  const dbctlConfigFilePath = `../../../cli/db/blueprint/mysql/opencartv1/${ENVIRONMENT}.yml`;
  const dbctlConfig = yaml.safeLoad(fs.readFileSync(dbctlConfigFilePath, 'utf8'));

  return dbctlConfig.secrets.vault.host;
}

async function getToken(name) {
  const vaultBaseUrl = getVaultBaseUrl();
  const options = {
    url: `${vaultBaseUrl}/v1/auth/cert/login`,
    cert: fs.readFileSync(CERT_FILE_PATH),
    key: fs.readFileSync(KEY_FILE_PATH),
    ca: fs.readFileSync(CACERT_FILE_PATH),
    form: JSON.stringify({
      "name": name
    })
  };
  const response = JSON.parse(await request.post(options));
  return response.auth.client_token;
}

function getCredentialEndpoint(role) {
  return `v1.1/${ORGANIZATION}/${TEAM}/db/hosted/mysql/${PRODUCT}/opencartv1/${ENVIRONMENT}/creds/${role}`;
}

async function getCredential(token, role) {
  const vaultBaseUrl = getVaultBaseUrl();
  const endpoint = getCredentialEndpoint(role);
  const options = {
    url: `${vaultBaseUrl}/v1/${endpoint}`,
    cert: fs.readFileSync(CERT_FILE_PATH),
    key: fs.readFileSync(KEY_FILE_PATH),
    ca: fs.readFileSync(CACERT_FILE_PATH),
    headers: {
      "X-Vault-Token": token
    }
  }
  const response = JSON.parse(await request.get(options));
  return response;
}

async function readCredential() {
  const name = `${ORGANIZATION}-${TEAM}-${PRODUCT}-${ENVIRONMENT}`;
  const role = "app";

  let token = "";
  try {
    token = await getToken(name);
  } catch (error) {
    console.log("Unable to get token", error.message);
  }
  try {
    const credentials = await getCredential(token, role);
    return credentials;
  } catch (error) {
    console.log("Unable to get credentials", error.message);
    return null;
  }
}

function writeCredentialToTemplate(username, password, inputPath, outputPath) {
  nunjucks.configure({ autoescape: true });
  const result = nunjucks.render(inputPath, {
    db_username: username,
    db_password: password,
    host: HOST,
    port: PORT
  });
  const outputFd = fs.openSync(outputPath, "w");
  fs.writeSync(outputFd, result);
  fs.closeSync(outputFd);
}

async function refreshCredential(currentTimestamp) {
  const credentials = await readCredential();
  
  if (credentials === null) {
    console.log("Failure happens in one of our processes, Aborting...");
    return;
  }

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
