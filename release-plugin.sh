SERVICE_NAME=$1

if [ -z $SERVICE_NAME ]; then
  echo "Usage ./release-plugin <service-name>"
  exit 1
fi

pushd $SERVICE_NAME
  if [ ! -f version.txt ]; then
    echo "Unable to locate version.txt in service directory"
    exit 1
  fi
  VERSION=$(cat version.txt)
popd

SERVICE_TMP_DIR=~/.pios
if [ ! -d $SERVICE_TMP_DIR ]; then
  mkdir $SERVICE_TMP_DIR 
fi

BRANCH_NAME=${SERVICE_NAME}-${VERSION}
GITHUB_BASE_URL=https://github.com/cermati/paylater-indodana-online-shop-plugin
pushd $SERVICE_TMP_DIR 
  if [ ! -d .git ]; then
    git init
    git remote add origin $GITHUB_BASE_URL
  fi
  
  git branch $BRANCH_NAME
  git checkout $BRANCH_NAME
popd

pushd $SERVICE_NAME
  cp -r plugin/* ~/.pios
popd

pushd $SERVICE_TMP_DIR
  git add .
  git commit -m "Add plugin for ${SERVICE_NAME} version ${VERSION}"
  git push origin $BRANCH_NAME

  git checkout master
  git branch -d $BRANCH_NAME
  rm -rf *
popd
