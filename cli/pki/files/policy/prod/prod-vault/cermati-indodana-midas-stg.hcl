
# ---------------------------------------------------------------------
# v1.1.0 - supported +
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/indodana/db/hosted/postgres/midas/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/midas/+/stg/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/midas/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/midas/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/rds/postgres/midas/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/midas/+/stg/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/midas/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/midas/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/apsara/postgres/midas/+/stg" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/midas/+/stg/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/midas/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/midas/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/kv/midas" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/kv/midas/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/aws/midas/+/stg" {
  capabilities = ["read", "create", "update", "list"]  
}

path "v1.1/cermati/indodana/aws/midas/+/stg/config/root" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/midas/+/stg/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/aws/midas/+/stg/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/auth/v1.1/cermati/indodana/midas/stg" {
  capabilities = ["read", "create", "update", "list", "sudo"]
}
path "auth/v1.1/cermati/indodana/midas/stg/role/*" {
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
# path "sys/mounts/v1.1.alt/db/hosted/postgres/cermati/indodana/midas/+/stg" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/midas/+/stg/config/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/midas/+/stg/roles/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/midas/+/stg/creds/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "sys/mounts/v1.1.alt/kv/cermati/indodana/midas" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.1.alt/kv/cermati/indodana/midas/*" {
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
# path "v1.0/cermati/indodana/db/hosted/postgres/config/stg-midas-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.0/cermati/indodana/db/hosted/postgres/roles/stg-midas-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
#
# path "v1.0/cermati/indodana/db/hosted/postgres/creds/stg-midas-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "sys/mounts/v1.0/cermati/indodana/kv/midas" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "v1.0/cermati/indodana/kv/midas/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------
