
# ---------------------------------------------------------------------
# v1.1.0 - supported +
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# DB Secrets Engine ACL
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/indodana/db/hosted/postgres/pios/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/pios/+/stg/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/pios/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/pios/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/rds/postgres/pios/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/pios/+/stg/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/pios/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/pios/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/apsara/postgres/pios/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/pios/+/stg/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/pios/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/pios/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

# -- Apsara Staging -- #
path "sys/mounts/v1.1/cermati/indodana/db/apsara/postgres/staging/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/staging/+/stg/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/staging/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/staging/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}
# -- -------------- -- #

# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# K/V Secrets Engine ACL
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/indodana/kv/pios" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/kv/pios/*" {
  capabilities = ["read", "create", "update", "list"]
}
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# AWS Secrets Engine ACL
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/indodana/aws/pios/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/pios/+/stg/config/root" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/pios/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/pios/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# Authentication ACL
# ---------------------------------------------------------------------
path "sys/auth/v1.1/cermati/indodana/pios/stg" {
  capabilities = ["read", "create", "update", "list", "sudo"]
}
path "auth/v1.1/cermati/indodana/pios/stg/role/*" {
  capabilities = ["read", "create", "update", "list"]
}
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# Administration Transparency ACL
# ---------------------------------------------------------------------
path "sys/auth" {
  capabilities = ["read", "list"]
}

path "sys/mounts" {
  capabilities = ["read", "list"]
}
path "sys/policies/acl" {
  capabilities = ["read", "list"]
}
path "sys/policies/acl/*" {
  capabilities = ["read"]
}
# ---------------------------------------------------------------------


# # ---------------------------------------------------------------------
# # v1.1.0-alt version - can be used with Vault v1.1.+ the old-structure
# # ---------------------------------------------------------------------
# path "sys/mounts/v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/stg" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/stg/config/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/stg/roles/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/stg/creds/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "sys/mounts/v1.1.alt/kv/cermati/indodana/pios" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/kv/cermati/indodana/pios/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------


# # ---------------------------------------------------------------------
# # before v1.1.0 - not supported + yet, the structure is a bit different
# # ---------------------------------------------------------------------
# path "sys/mounts/v1.0/cermati/indodana/db/hosted/postgres" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.0/cermati/indodana/db/hosted/postgres/config/stg-pios-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.0/cermati/indodana/db/hosted/postgres/roles/stg-pios-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.0/cermati/indodana/db/hosted/postgres/creds/stg-pios-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "sys/mounts/v1.0/cermati/indodana/kv/pios" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "v1.0/cermati/indodana/kv/pios/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------
