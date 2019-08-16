const nunjucks = require("nunjucks");
const request = require("request-promise");
const fs = require("fs");
const dotenv = require("dotenv");
const yaml = require("js-yaml");

dotenv.config({
  path: "../../../cli/NAMESPACE"
});

const ORGANIZATION = process.env.ORGANIZATION;
const TEAM = process.env.TEAM;
const PRODUCT = process.env.PRODUCT;
const ENVIRONMENT = process.env.ENVIRONMENT;

function getVaultBaseUrl() {
  const dbctlConfigFilePath = `../../../cli/db/blueprint/mysql/opencartv2/${ENVIRONMENT}.yml`;
  const dbctlConfig = yaml.safeLoad(fs.readFileSync(dbctlConfigFilePath, 'utf8'));

  return dbctlConfig.secrets.vault.host;
}

async function getToken(name, certificates) {
  const vaultBaseUrl = getVaultBaseUrl();
  const options = {
    ...certificates,
    url: `${vaultBaseUrl}/v1/auth/cert/login`,
    form: JSON.stringify({
      "name": name
    })
  };
  const response = JSON.parse(await request.post(options));
  return response.auth.client_token;
}

function getCredentialEndpoint(role) {
  return `v1.1/${ORGANIZATION}/${TEAM}/db/hosted/mysql/${PRODUCT}/opencartv2/${ENVIRONMENT}/creds/${role}`;
}

async function getCredential(token, role, certificates) {
  const vaultBaseUrl = getVaultBaseUrl();
  const endpoint = getCredentialEndpoint(role);
  const options = {
    ...certificates,
    url: `${vaultBaseUrl}/v1/${endpoint}`,
    headers: {
      "X-Vault-Token": token
    }
  }
  const response = JSON.parse(await request.get(options));
  return response;
}

async function readCredential() {
  const servicesCertificatePath = `/usr/share/pki/certs/service`
  const cacertFilePath = `${servicesCertificatePath}/pios-${ENVIRONMENT}/pios-${ENVIRONMENT}.ca.crt`;
  const certFilePath = `${servicesCertificatePath}/pios-${ENVIRONMENT}/pios-${ENVIRONMENT}.crt`;
  const keyFilePath = `${servicesCertificatePath}/pios-${ENVIRONMENT}/.private/pios-${ENVIRONMENT}.key`;

  const certificates = {
    ca: fs.readFileSync(cacertFilePath),
    cert: fs.readFileSync(certFilePath),
    key: fs.readFileSync(keyFilePath)
  }

  const name = `${ORGANIZATION}-${TEAM}-${PRODUCT}-${ENVIRONMENT}`;
  const role = "app";

  let token = "";
  try {
    token = await getToken(name, certificates);
  } catch (error) {
    console.log("Unable to get token", error.message);
  }
  try {
    const credentials = await getCredential(token, role, certificates);
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
    host: process.env.HOST,
    port: process.env.PORT
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

  const configTemplatePath = "config.njk";
  const configPath = "upload/config.php";
  const adminConfigTemplatePath = "admin.config.njk";
  const adminConfigPath = "upload/admin/config.php";

  writeCredentialToTemplate(
    credentials.data.username, 
    credentials.data.password, 
    configTemplatePath, 
    configPath
  );
  writeCredentialToTemplate(
    credentials.data.username, 
    credentials.data.password, 
    adminConfigTemplatePath, 
    adminConfigPath
  );

  refreshTimestamp = currentTimestamp + credentials.lease_duration * 1000;
  console.log("Next Credential Refresh: " + new Date(refreshTimestamp));

  setTimeout(function() {
    const currentTimestampForNextRefresh = Date.now();
    refreshCredential(currentTimestampForNextRefresh);
  }, refreshTimestamp - currentTimestamp);
}

const currentTimestamp = Date.now();
refreshCredential(currentTimestamp);
