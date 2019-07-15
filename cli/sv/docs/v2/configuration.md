# SVCTL Configuration Manual

This document outlines how to create service configurations for SVCTL and what
are there to configure.

See the [configuration specification](https://docs.google.com/document/d/1hjtF2ao5KxqYWHfbAv7H4ZdPHNOioAk7tsuSN5xg0nA/edit#).

##### Table of Contents
- [Quick Start](#quick-start)
- [SVCTL Configuration Directories](#config-dirs)
- [Environment-Specific Configuration](#env-config)
  - [Separation by file](#env-config-file)
  - [Separation by YAML field](#env-config-field)
- [Configuration Inheritance](#config-inheritance)
- [Configuration Merging](#config-merging)
- [Configuration Specifications](#config-spec)
  - [Service Scripts](#config-spec-scripts)
  - [Kubernetes Clusters/Contexts Configuration](#config-spec-kube-cluster)
  - [Service Metadata Configurations](#config-spec-service-meta)
  - [PKI Configurations](#config-spec-service-pki)
  - [Docker Configurations](#config-spec-service-docker)
  - [Kubernetes Configurations](#config-spec-service-kube)
  - [Environment Configurations](#config-spec-service-env-configs)

--------------------------------------------------------------------------------

## Quick Start<a name="quick-start" />

1.  Install BCL from [sdsdkkk/bcl](https://github.com/sdsdkkk/bcl/).


2.  Create a `BCLFile` in the root directory of your repository containing _at
    least_ these lines:
  
    ```
    git@github.com:cermati/devops-toolkit-release.git
    sv-vX.Y.Z-release
    ```

    And install SVCTL:

    ```
    $ cd $PROJECT_DIR
    $ bcl package install
    ```

    SVCTL is now installed in `$PROJECT_DIR/cli/svctl`


3.  Run SVCTL for the first time:

    ```
    $ ./svctl init
    [ERROR] SVCTL config file svctl.yml did not exist and has been created in
    $PROJECT_DIR/cli. Please review that file's content before running any SVCTL
    command.
    ```

    If you're installing SVCTL through BCL, you can ignore that message. If not,
    you'll need to configure the `svctl.yml` file manually.


4.  Initialize SVCTL configurations and service:

    ```
    $ ./svctl init
    $ ./svctl init-service your-service-name
    ```

    SVCTL is now installed with default configurations with a service
    `your-service-name`. See the next section about list of configurations.

--------------------------------------------------------------------------------

## SVCTL Configuration Directories<a name="config-dirs" />

SVCTL uses [YAML](https://www.tutorialspoint.com/yaml/yaml_basics.htm) files as
its configuration format. There are two types of configurations:
  - **Global configurations**: YAML files that are located directly under the
    `$PROJECT_DIR/.services.d/` directory. Global configurations will be
    inherited by all services, and can be overwritten if a conflicting
    configuration is defined in service configurations. Usually only contains
    Kubernetes cluster configurations.

  - **Service configurations**: YAML files that are located inside the
    `$PROJECT_DIR/.services.d/<service-name>/` directory. All YAML files inside
    this directory are loaded _recursively_. These configurations are scoped to
    `service-name` only.

--------------------------------------------------------------------------------

## Environment-Specific Configuration<a name="env-config" />

You can configure a subset of configurations to only appear or have different
values depending on runtime environments. Currently there are two kinds of
environments:
  - `runtime`, the usual runtime/deployment environment, e.g. `development`,
    `staging`, `production`.

  - `kubernetes_context`, information about which Kubernetes cluster the service
    is about to be deployed. Only available on `./svctl kube` subcommands.
    Configurations that specifies that they'll only be loaded on a specific
    Kubernetes environment _will not be loaded_ on commands that lacks
    Kubernetes environment (e.g. `./svctl build`).

There are two methods of creating environment-specific configurations:
  - Separation by file
  - Separation by YAML field


### Separation by file<a name="env-config-file" />

This means that we can craft a configuration file so that it will only be loaded
on a specific environment. This can be done by adding a comment somewhere in the
beginning of the file:

```yaml
$ cat a_yaml_config_only_for_staging_cluster01-indodana.yml

---
# __ENVIRONMENT__ runtime=staging, kubernetes_context=cluster01-indodana

description: |
  This YAML file will only be loaded when the current runtime environment is
  `staging` and the Kubernetes context is `cluster01-indodana`
```

```yaml
$ cat a_yaml_config_only_for_staging.yml

---
# __ENVIRONMENT__ runtime=staging

description: |
  This YAML file will only be loaded when the current runtime environment is
  `staging`. Any Kubernetes contexts will do. You can also use
  `kubernetes_context=*` to explicitly tell SVCTL that this file will be loaded
  regardless of the value of `kubernetes_context`.
```

### Separation by YAML field<a name="env-config-field" />

There might be times where separation by file is a tad overkill. To handle that,
SVCTL provide a special YAML tag `!env` which will select the appropriate value
depending on environments:

```yaml
---

an_environment_specific_field: !env
  - conditions:
      runtime: staging
      kubernetes_context: cluster01-indodana
    value: will be loaded when runtime=staging and kubernetes_context=cluster01-indodana

  - conditions:
      runtime: staging
    value: will be loaded when runtime=staging, kubernetes_context does not matter

  - conditions:
      runtime: production
      kubernetes_context: "*"
    value: will be loaded when runtime=production, kubernetes_context does not matter

  - conditions:
      runtime: development
    value: will be loaded when runtime=development, kubernetes_context does not matter
```

SVCTL will iterate the list one by one, and stopping on the first conditions
that matched. The value of the tagged field (`an_environment_specific_field`)
will be substituted by the value of the `value` field on the matched object.

--------------------------------------------------------------------------------

## Configuration Inheritance<a name="config-inheritance" />

Sometimes you might want to configure a service to inherit configurations from
another service, for example:
  - We have two base configurations, e.g. `midas-base` and `midas-worker-base`
  - Service `midasapp` and `creditapp` might want to extend `midas-base` to
    minimize config duplication, while a worker service `midasworker` might want
    to extend both `midas-base` and `midas-worker-base`

You can do this with SVCTL by adding a YAML field `config_extends` in any YAML
file:

```yaml
config_extends: midas-base

# or

config_extends:
  - midas-base
  - midas-worker-base
```

--------------------------------------------------------------------------------

## Configuration Merging<a name="config-merging" />

When conflicting YAML fields are present, either because there are two identical
fields on multiple files or due to config inheritance, by default SVCTL will
merge those in similar fashion as Python's `dict.update()`
[method](https://docs.python.org/3/library/stdtypes.html#dict.update):

  - If the field types differ (map merging into a string, list merging into a
    map, etc), an error will be thrown

  - If the field is a map, the fields of said map will be combined with this
    rule recusively

  - If the field is a primitive (e.g. string, int) or a list, the value of the
    extended service field will be overwritten

Please note that when there are two conflicting fields in the same service, the
merging order is undefined.

This merging strategy can be changed by using YAML tags:

  - `!merge`, the default behavior described above

  - `!merge:lossless`, similar to `!merge` but will raise an error if a non-null
    field is about to be overwritten

  - `!replace`, completely replace the value defined in extended service's
    configurations. Applicable to all field types

  - `!append`, append the elements of the list after the list elements defined
    in extended service's configurations. Applicable only to lists

  - `!prepend`, append the elements of the list after the list elements defined
    in extended service's configurations. Applicable only to lists

  - `!lpatch:<key>`, replace list element(s) according to `key`. This merge
    strategy behave similarly to Kubernetes' strategic merge with merge key. See
    example below. Applicable only to lists with map elements.

Example of merging strategies' behaviors:

```yaml
--------------------------------------------------------------------------------
# Contents of extended-service/main.yml

map0:
  k1: v1
  k2: v2
  nk: # a null key

map1:
  k1: v1
  k2: v2

map2:
  k3: v3
  k4: v4

list1: [1, 2, 3, 4]

list2: [5, 6, 7, 8]

list3:
  - name: aa
    value: va
    another_value: a_va
    a_list:
      - 1
      - 2

  - name: bb
    value: vb

  - name: cc
    value: vc


--------------------------------------------------------------------------------
# Contents of extending-service/main.yml

config_extends:
  - extended-service

map0: !merge:lossless
  nk: nv

map1: !merge
  nk: nv

map2: !replace
  nk: nv

list1: !append
  - 10
  - 20

list2: !prepend
  - 10
  - 20

list3: !lpatch:name
  - !merge
    name: aa
    value: new_va
    a_list: !prepend
      - 10
      - 20

  - !replace
    name: cc
    new_value: new_vc

--------------------------------------------------------------------------------
# Combined values

map0:
  k1: v1
  k2: v2
  nk: nv

map1:
  k1: v1
  k2: v2
  nk: nv

map2:
  nk: nv

list1: [1, 2, 3, 4, 10, 20]

list2: [10, 20, 5, 6, 7, 8]

list3:
  - name: aa
    value: new_va
    another_value: a_va
    a_list:
      - 10
      - 20
      - 1
      - 2

  - name: bb
    value: vb

  - name: cc
    new_value: new_vc
```

--------------------------------------------------------------------------------

## Configuration Specifications<a name="config-spec" />

This section describes all configuration options in SVCTL.

Fields marked `optional` does not need to be specified in the configuration
file(s).

### Service Scripts<a name="config-spec-scripts" />

A file called `service` should be generated automatically in the
`.services.d/<service>/` directory. This file is a Bash script that contains
several functions regarding how to:
  - build the service
  - run the service
  - install runtime dependencies of the service
  - prepare the working before building a Docker image of the service

For each service, some functions need to be defined:
  - `build()`: Script to build the service into portable artifacts (e.g. static
    JS files, JAR files, or in NodeJS case: the whole project directory).
    Accepts three arguments: `SERVICE_NAME`, `ENVIRONMENT`, and
    `BUILD_DIRECTORY`.
    
    This function **will be executed in the build image container**. This
    function **will be executed from the root project directory**. All build
    artifacts should be stored into `BUILD_DIRECTORY` directory.

  - `run()`: Script to run the service from the built artifacts. Accepts _at
    least_ three arguments: `SERVICE_NAME`, `ENVIRONMENT`, and
    `BUILD_DIRECTORY`. Additional arguments for the function can be passed by
    passing `--` followed by the list of additional arguments, for example:

    `./svctl run a_service an_env -- pos_arg1 "pos arg2" --key1 value1`

    The `run()` function will receive arguments like this:
    - `$1` = `a_service`
    - `$2` = `an_env`
    - `$3` = `<build directory>`
    - `$4` = `pos_arg1`
    - `$5` = `pos arg2`
    - `$6` = `--key1`
    - `$7` = `value1`

    This function **will be executed from the `BUILD_DIRECTORY`**.

There is also several optional functions that a service can implement but not
required to:

  - `postbuild`(): Script to install dependencies that are not portable but are
    not installed system-wide (e.g. Python `virtualenv` directory, NodeJS
    `node_modules`). Accepts three arguments: `SERVICE_NAME`, `ENVIRONMENT`, and
    `BUILD_DIRECTORY`.
    
    This function **will be executed in the base image container**, _not_ in the
    build image container. This function **will be executed from the
    `BUILD_DIRECTORY`**.

  - `host_prebuild()`: Script to prepare the project directory with build-time
    dependencies that are temporary. This script **will run in the host
    machine** before the Docker build procedure is invoked.
    
    Some uses of this function are:
    - Copy files to the project's directory that are otherwise not present there
      (e.g. `.npmrc`, AWS credentials, etc.)

    - Run some build steps that depend on environments that aren't present in
      the Docker build container

    This function accepts three arguments: `SERVICE_NAME`, `ENVIRONMENT`, and
    `BUILD_DIRECTORY`; and **will be executed from the root project directory**.

  - `host_postbuild()`: Similar to `host_prebuild()`, a script that **will run
    in the host machine**, after the Docker build procedure has finished. This
    function might be useful to clean up additional build artifacts created
    by `host_prebuild()`.
    
    This function accepts three arguments: `SERVICE_NAME`, `ENVIRONMENT`, and
    `BUILD_DIRECTORY`; and **will be executed from the root project directory**.

Explanation of function arguments:
  - `SERVICE_NAME`: The name of the service.

  - `ENVIRONMENT`: The environment of the service.

  - `BUILD_DIRECTORY`: The directory that should contain the resulting build
      artifacts. Is equal to `.build/$SERVICE_NAME/$ENVIRONMENT`.

For NodeJS services the file might look like this:

```bash
#!/bin/bash

set -e

build() {
  local SERVICE_NAME=$1
  local ENVIRONMENT=$2
  local BUILD_DIRECTORY=$3

  local O_IFS="$IFS"

  IFS=$'\n'
  local files=(`git ls-files`)
  local untracked_files=(`git ls-files --others --exclude-standard`)

  echo "Copying non-gitignored files..."
  for file in "${files[@]}"; do
    cp --parents "$file" "$BUILD_DIRECTORY"
  done

  echo "Copying untracked files..."
  for file in "${untracked_files[@]}"; do
    cp --parents "$file" "$BUILD_DIRECTORY"
  done

  IFS="$O_IFS"

  pushd "$BUILD_DIRECTORY"

  echo "Creating empty .env file..."
  touch .env

  popd
}

postbuild() {
  local SERVICE_NAME=$1
  local ENVIRONMENT=$2
  local BUILD_DIRECTORY=$3

  echo "Running npm install..."
  npm install --production
}

run() {
  local SERVICE_NAME=$1
  local ENVIRONMENT=$2
  local BUILD_DIRECTORY=$3

  local ALL_ARGS=( "$@" )
  local SERVICE_ARGS=("${ALL_ARGS[@]:3}")

  exec npm run start -- "${SERVICE_ARGS[@]}"
}
```

### Kubernetes Clusters/Contexts Configuration<a name="config-spec-kube-cluster" />

While you can define any kind of configurations in as global configurations, by
default SVCTL will only put one `kubernetes_contexts.yml` file that contains
list of Kubernetes contexts (pair of cluster and namespace) under the
`.services.d` directory.

NOTE: In this document, a Kubernetes context and Kubernetes cluster might be
used interchangeably.

```yaml
kubernetes:
  # This section contains mapping between Kubernetes context name and its
  #  parameters
  clusters:
    # Defines a context called "cluster01-midas-stg", which will connect to
    #  Kubernetes API server at https://k8s-cluster01.indodana.com, which is a
    #  managed Aliyun cluster, and will use cluster namespace
    #  "cermati-indodana-midas-stg".
    cluster01-midas-stg:
      url: https://k8s-cluster01.indodana.com
      namespace: cermati-indodana-midas-stg
      cloud_vendor: aliyun

    cluster01-midas-prod:
      url: https://k8s-cluster01.indodana.com
      namespace: cermati-indodana-midas-prod
      cloud_vendor: aliyun

    cluster01-credit-stg:
      url: https://k8s-cluster01.indodana.com
      namespace: cermati-indodana-credit-stg
      cloud_vendor: aliyun
```

This configuration is recommended to be scoped globally.

### Service Metadata Configurations<a name="config-spec-service-meta" />

By default located in the `.services.d/<service>/meta.yml` file, these
configurations contains metadata about the service.

```yaml
# The schema version of this configuration, should not be changed manually
version: v2

# (Optional) Indicates that this service's configuration extends another
#   service's, thus inheriting all of its configurations
config_extends: extended-service

# Parent organization of this service, case-sensitive. Currently, the only valid
#  value for this field is "cermati"
organization: cermati

# The team this service belongs to, case-sensitive.
#  Valid values: "cermati", "indodana"
team: indodana

# Name of the product this service belongs to, case-sensitive. Can be equal to
#  the name of the service if it is a standalone service.
#  Example: "athena-service" and "athena-worker" both belongs to "athena"
#           product
product: midas

# List of build and runtime environments of this service. You can rename the
#  environments (e.g. from 'production' to 'prod') by changing the values here
environments:
  - development
  - staging
  - production

# Configurations about the Git repository of this project
git:
  # Remote configuration
  remote: git@github.com:cermati/midas.git

  # Mapping between environments and their respective git branch
  branches:
    development: master
    staging: staging
    production: production

# (Optional) Configurations about Jenkins. Any changes in this section should be
#  followed by reconfiguration of the pipelines using
#  ./svctl jenkins configure-pipeline containerize <service-name>
jenkins:
  # (Optional) Credential IDs used by the pipelines associated with this
  #  service. Contact the Infra team if your service needs different credential.
  credential_ids:
    # (Optional) GitHub credential used to pull this repository. Defaults to
    #  null, which will uses the default credential defined in Jenkins'
    #  configuration
    github:

    # (Optional) Docker registry credential used to pull image of this service.
    #  Defaults to null, which will uses the default credential defined in
    #  Jenkins' configuration
    docker:
```

### PKI Configurations<a name="config-spec-service-pki" />

By default also located in the `.services.d/<service>/meta.yml` file, these
configurations describe how PKICTL's Public Key Infrastructure is used by this
service.

```yaml
# (Optional) Information about Public Key Infrastructure utilization of this
#  service
pki:
  # (Optional) Set to true if this service needs PKICTL certificate to exist in
  #  the default directory (/usr/share/pki/certs/service). Default is false.
  enabled: true

  # (Optional) Prefix of the PKICTL blueprint of this service. Only to be used
  #  when the service name defined in this repository differs with the one
  #  defined in PKICTL's blueprints. This value will be used as a prefix in the
  #  actual blueprint name according to this format:
  #
  #    <pkictl_blueprint_prefix>-<environment>
  #
  #  By default the value is "<service_name>"
  pkictl_blueprint_prefix: midasapp
```

### Docker Configurations<a name="config-spec-service-docker" />

By default located in the `.services.d/<service>/docker.yml` file, these
configurations describe how to build a Docker image of the service.

```yaml
# Configuration about containerization process using Docker
containerize:
  docker:
    # (Optional) Repository to push and pull service image from. If not defined,
    #  will be inferred automatically from `team`
    repository: 1234567890.dkr.ecr.us-west-2.amazonaws.com/non-existing-repository

    # (Optional) Information about Docker image that will be used to build the
    #  image. Consult the infra team about what image your service should use.
    builder_image:
      # (Optional) Name and tag of the Docker image (without namespace). If not
      #  defined, will be inferred from service name
      name: openjdk:11.0.3-jdk-slim-stretch

      # (Optional) Repository to pull the image from. Default is the value of
      #  `repository`
      repository: registry.hub.docker.com/library

      # (Optional) User of the image. Default is `appRunner`
      user: root

      # (Optional) A directory in the project root directory where directories
      #  defined in `build_time_mounts[*].mount_directory` will be temporarily
      #  copied before being mounted into the build container.
      #
      #  This directory **should not** be listed in the `.gitignore` file unless
      #  a `.dockerignore` file exist which **does not list** this directory
      #  (i.e. if this project has a `.dockerignore` file, you can ignore this
      #  directory in the `.gitignore` but not in the `.dockerignore` file).
      #
      #  The default value for this field is `.svctl_docker_mount`
      temporary_mount_directory: .svctl_docker_mount

      # (Optional) This section defines directories that are present in the host
      #  machine that will be mounted (copied) into the **build container** at
      #  the Docker build process.
      #
      #  These files won't be present in the resulting Docker image unless
      #  somehow you managed to mount the files into the build directory. If
      #  that's what you want, you should copy the mounted files during the
      #  `build()` command in the `service` file.
      #
      #  This section is an array. Multiple mount configurations can be defined.
      mounts:
        # (Required) A file/directory path in the project root path which files
        #  will be mounted (copied) into the build container. Can be absolute
        #  path or relative path (relative to the project's root path)
        - mount_path: config_dir

        # (Optional) Where should the `mount_path` be mounted/copied into. Can
        #  be absolute or relative path (relative to the project's root path).
        #  The default value is `/`, which means if there is a file in
        #  `/path/to/project/config_dir/dir1/dir2/a_file.txt`, that file will be
        #   present in the build container at `/dir1/dir2/a_file.txt`.
          target_path: /

        # Another example, `/usr/share/pki/certs/member` will be copied as
        #  `/usr/share/pki/certs/member`
        - mount_path: /usr/share/pki/certs/member
          target_path: /usr/share/pki/certs/member

      # (Optional) This section defines list of build-time secrets that will be
      #  mounted (either as files or environment variabels) into the build
      #  container when executing `./svctl docker containerize`. Currently, only
      #   KV v2 secrets from Hashicorp Vault are supported
      #
      #  This section is an array. Multiple secret definitions can be defined.
      secrets:
        # Example of a secret from Vault provider. The value of "content" key of
        #  "v1.1/cermati/indodana/kv/chermes/stg/npmrc" KV will be stored as
        #  file in ".npmrc" file, relative to the project root directory
        - vault:
            # The path of the KV v2 secret
            path: v1.1/cermati/indodana/kv/chermes/stg/npmrc

            # Role policy to use to authenticate to Vault
            role: cermati-indodana-chermes-stg

            # (Optional) The key of the KV to fetch the value of. If this is not
            #  defined, then the entirety of the KV map will be fetched.
            key: content

            # How to mount the secret. There are two types of mount:
            #   - type: file. Mount the secret as a file. If `key` is specified,
            #     then the value of said key will be the content of the file. If
            #     `key` is not specified, then the KV map will be written to the
            #     file in DOTENV format.
            #     Needs to have a `path:` field defined, which is a path to the
            #     file (either absolute or relative to the project root
            #     directory)
            #
            #   - type: env. Mount the secret as environment variable(s). An
            #     `name:` field is used to determine the name of the environment
            #     variable set.
            #     If `key` is specified, then `name` field is mandatory and will
            #     be used as the name of the environment variable which contains
            #     the value of the value of the key. If `key` is not specified,
            #     then `name` is optional and all KV pairs will be set as
            #     environment variables, with the `name` field acting as a
            #     prefix to the KV key name, which will be used as the
            #     environment variable name.
            mount:
              type: file
              path: .npmrc

        # Example of a secret from Vault provider. The value of "a_key" key of
        #  "v1.1/cermati/indodana/kv/chermes/stg/build_time_secrets" KV will be
        #  loaded as environment variable called "A_KEY"
        - vault:
            path: v1.1/cermati/indodana/kv/chermes/stg/build_time_secrets
            role: cermati-indodana-chermes-stg
            key: a_key
            mount:
              type: env
              name: A_KEY

        # Example of a secret from Vault provider. All KV values of of
        #  "v1.1/cermati/indodana/kv/chermes/stg/environment_variables" KV will be
        #  loaded as environment variables (with no prefix as `name` is null).
        - vault:
            path: v1.1/cermati/indodana/kv/chermes/stg/environment_variables
            role: cermati-indodana-chermes-stg
            mount:
              type: env
              name:

      # (Optional) List of environment variables defined in the host machine that
      #  will be passed into the **build** container as environment variables
      inherited_host_env:
        - GITHUB_USERNAME
        - PKICTL_MEMBER_CERT_FILE
        - PKICTL_MEMBER_KEY_FILE
        - PKICTL_MEMBER_CA_CERT_FILE
        - PKICTL_MEMBER_PKCS12_FILE

    # (Optional) Information about Docker image that will be used to run the
    #  image. Consult the infra team about what image your service should use.
    runtime_image:
      # (Optional) Name and tag of the Docker image (without namespace). If not
      #  defined, will be inferred from service name
      name: openjdk:11.0.3-jre-slim-stretch

      # (Optional) Repository to pull the image from. Default is the value of
      # `repository`
      repository: registry.hub.docker.com/library

      # (Optional) User of the image. Default is `appRunner`
      user: root

      # (Optional) Working directory. You should not need to change this.
      #  Default is `/home/<base_image.user>/<service_name>/.build/<service_name>/<environment>`
      workdir: /root/midasapp/.build/midasapp/production
```

### Kubernetes Configurations<a name="config-spec-service-kube" />

By default located in the `.services.d/<service>/kubernetes` directory, these
configurations describe how to deploy this service to Kubernetes. Almost all
fields in these configurations are **optional**, but some are required to make
the service functional (e.g. number of replicas, what ports to expose).

There are two configuration groups, one regarding Kubernetes Deployment, and one
regarding Kubernetes Service.

Kubernetes Deployment:

```yaml
# (Optional) Kubernetes-related configurations
kubernetes:
  # (Optional) Configurations related to deployment to Kubernetes
  spec:
    # This section contains simplified Kubernetes "Deployment v1 apps"
    #  definition.
    #
    #  All fields in this section are optional, but some are required to make
    #  the service functional (e.g. pod.replicas, pod.ports, pod.logging, etc.).
    #
    #  Some fields conform to one of the official Kubernetes API object and will
    #  be marked accordingly. Reference to said API objects can be viewed here:
    #    https://kubernetes.io/docs/reference/generated/kubernetes-api/v1.12/
    deployment:
      # The `template` section contains simplified Kubernetes deployment
      #  definition
      template:
        deployment:
          # The `deployment.labels` section contains labels that will be
          #  attributed to the deployment. This section conforms to the `labels`
          #  object of "ObjectMeta v1 meta" API.
          labels:
            a_custom_deployment_label: this_will_be_overwritten

        pod:
          # The `pod.replicas` section determines the number of pods (analogue
          #  of containers or processes in traditional terms) that will be
          #  running in a Kubernetes cluster. This section conforms to the
          #  `replicas` object of "DeploymentSpec v1 apps" API.
          replicas: 3

          # The `pod.labels` section contains labels that will be attributed to
          #  the pod. This section conforms to the `labels` object of
          #  "ObjectMeta v1 meta" API.
          labels:
            a_custom_pod_label: pod_label

          # The `pod.requests` section contains specification regarding resource
          #  requests. This section conforms to the `requests` object of the
          #  "ResourceRequirements v1 core" API. See:
          #  https://kubernetes.io/docs/concepts/configuration/manage-compute-resources-container/
          requests:
            cpu: 500m
            memory: 512Mi

          # The `pod.ports` section contains list of ports that will be exposed
          #  by the pod. Each object in this list conforms to the "ContainerPort
          #  v1 core" API.
          ports:
            - name: http-api
              containerPort: 3005

            - name: healthz
              containerPort: 3006

          # The `pod.mainContainer` section contains optional specifications
          #  regarding the container that contains the actual service that
          #  cannot be expressed using any other fields. This section conforms
          #  to the "Container v1 core" API.
          mainContainer:
            args:
              - --
              - server
              - midasapp

          # The `pod.sidecarContainers` section contains list of additional
          #  containers that will be run alongside the main container. Each
          #  object of this list sould conform to the "Container v1 core" API.
          # 
          #  Unless you know what you're doing, skip this configuration.
          sidecarContainers:
            - name: custom-sidecar
              image: ubuntu:18.04

          # The `pod.logging` section contains configuration about log shipping
          #  that applies to all containers in this pod.
          logging:
            # Which log parser to use. Contact the infra team if your service
            #  use different log format. Default is "nodejs_cermati-utils_v1"
            #  (the log format outputted by @cermati/cermati-utils/logger Node
            #  module)
            parser: nodejs_cermati-utils_v1

            # Whether to discard the logs generated by this service or not. Must
            #  be surrounded by quotation marks. Default is "false".
            exclude: "false"

            # Logging namespace of this service. Two services sharing the same
            #  namespace will have their logs saved in the same Elasticsearch
            #  index. Should be prefixed with `service-`. Default value is
            #  `service-<organization>-<team>-<product>`
            namespace: service-cermati-indodana-midas

      # The `raw` section contains custom configurations that can't be
      #  configured within the `template` section. Fields defined in this
      #  section will be merged into the deployment specification generated from
      #  the `template` section. This section conforms to the "Deployment v1
      #  apps" API.
      #
      #  Unless you know what you're doing, skip this configuration.
      raw:
        metadata:
          labels:
            a_custom_deployment_label: deployment_label
            another_custom_deployment_label: another_deployment_label

        spec:
          strategy:
            type: RollingUpdate
            rollingUpdate: 
              maxUnavailable: 0
              maxSurge: 50%
```

Kubernetes Service:

```yaml
# (Optional) Kubernetes-related configurations
kubernetes:
  # (Optional) Configurations related to deployment to Kubernetes
  spec:
    # This section contains simplified Kubernetes "Service v1 core" definition.
    #
    #  All fields in this section are optional, but some are required to make
    #  the service functional.
    #
    #  Some fields conform to one of the official Kubernetes API object and will
    #  be marked accordingly. Reference to said API objects can be viewed here:
    #    https://kubernetes.io/docs/reference/generated/kubernetes-api/v1.12/
    service:
      # The `template` section contains simplified Kubernetes service definition
      template:
        # The `labels` section contains labels that will be attributed to the
        #  service. This section conforms to the `labels` object of "ObjectMeta
        #  v1 meta" API.
        labels:
          a_custom_service_label: a_custom_service_label_value

        # The `ports` section contains list of pod ports that will exposed by
        #  the service. Ports exposed here must be exposed first by the main
        #  container, configured in the `kubernetes.deployment.deployment
        #  .template.pod.ports` section. Each object within this list conforms
        #  to the "ServicePort v1 core" API.
        ports:
          - name: http-api
            port: 80
            targetPort: http-api  # refers to the `http-api` port defined in
                                  # main_container

          - name: healthz
            port: 3006
            targetPort: healthz   # refers to the `healthz` port defined in
                                  # main_container

        # The `type` section dictates the type of the Kubernetes service that
        #  will be created from this configuration. This section conforms to the
        #  `type` object of the "ServiceSpec v1 core" API.
        #  More info: https://kubernetes.io/docs/concepts/services-networking/service/#publishing-services-service-types
        type: LoadBalancer

        # The `loadBalancerOptions` section contains configuration relevant when
        #  using "LoadBalancer" service type.
        loadBalancerOptions:
          # What kind of loadbalancer service to create. "internal" means that
          #  the service will only be accessible within the VPC. "external"
          #  means that the service will be exposed to the public internet.
          type: internal

      # The `raw` section contains custom configurations that can't be
      #  configured within the `template` section. Fields defined in this
      #  section will be merged into the deployment specification generated from
      #  the `template` section. This section conforms to the "Service v1 core"
      #  API.
      #
      #  Unless you know what you're doing, skip this configuration.
      raw:
        spec:
          sessionAffinity: ClientIP
          sessionAffinityConfig:
            clientIP:
              timeoutSeconds: 10800
```

### Environment Configurations<a name="config-spec-service-env-configs" />

By default located in the `.services.d/<service>/config/<env>/config.yml` file,
these configurations describe environment-specific files, secrets, and
environment variables (subsequently referred as _"config"s_) that are required by
the service to run.

The fields of this section is as follows:
  - `configs`. This top-level field is a map that contains valid config sources,
    currently the only valid value is "static".

    - `configs.static`. List of mappings about configs that originate from 
      static files located within the project's root directory. This section is
      a list, and each element specifies how to load a file or directory.

      Each mapping in this list contains two fields:

      - `path`: relative path to the file located in the project root
        directory

      - `mount`: how to mount the file into Kubernetes. Contains two fields:
        - `type`: how to mount the file, either `env`, `file` or `directory`.

          If `env`, the file is assumed to be in DOTENV format and will be
          loaded as environment variables.

          If `file`, the file will be mounted into the service's container, in
          the service's working directory.
          
          If `directory`, all files inside `path` will be mounted into
          `mount.path` while keeping the original directory structure

        - `path`: only used when `type` is `file` or `directory`. Specifies
          the path to where the file should be mounted, relative to the
          service's working directory

```yaml
configs:
  static:
    # Example: load `config/production/environment` DOTENV file to environment
    #          variables
    - path: config/production/environment
      mount:
        type: env

    # Example: load config file `config/production/dotenv` into working
    #          directory as `.env`
    - path: config/production/dotenv
      mount:
        type: file
        path: .env

    # Example: load every file in `config/production/dir` directory and mount
    #          those as files in the runtime directory `.`
    - path: config/production/dir/
      mount:
        type: directory
        path: .
```
