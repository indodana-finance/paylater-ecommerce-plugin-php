
# ---------------------------------------------------------------------
# v1.1.0 - supported +
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# DB Secrets Engine ACL
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/indodana/db/hosted/postgres/pios/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/pios/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/pios/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/pios/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/rds/postgres/pios/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/pios/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/pios/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/pios/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/apsara/postgres/pios/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/pios/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/pios/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/pios/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/hosted/mysql/pios/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/mysql/pios/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/mysql/pios/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/mysql/pios/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/rds/mysql/pios/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/mysql/pios/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/mysql/pios/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/mysql/pios/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/apsara/mysql/pios/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/mysql/pios/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/mysql/pios/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/mysql/pios/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

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
path "sys/mounts/v1.1/cermati/indodana/aws/pios/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/pios/+/dev/config/root" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/pios/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/pios/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# Authentication ACL
# ---------------------------------------------------------------------
path "sys/auth/v1.1/cermati/indodana/pios/dev" {
  capabilities = ["read", "create", "update", "list", "sudo"]
}
path "auth/v1.1/cermati/indodana/pios/dev/role/*" {
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
# path "sys/mounts/v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/dev" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/dev/config/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/dev/roles/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/pios/+/dev/creds/*" {
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
# path "v1.0/cermati/indodana/db/hosted/postgres/config/dev-pios-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.0/cermati/indodana/db/hosted/postgres/roles/dev-pios-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.0/cermati/indodana/db/hosted/postgres/creds/dev-pios-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "sys/mounts/v1.0/cermati/indodana/kv/pios" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "v1.0/cermati/indodana/kv/pios/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------
