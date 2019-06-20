
# ---------------------------------------------------------------------
# v1.1.0 - supported +
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/indodana/db/hosted/postgres/athena/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/athena/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/athena/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/hosted/postgres/athena/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/rds/postgres/athena/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/athena/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/athena/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/rds/postgres/athena/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/db/apsara/postgres/athena/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/athena/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/db/apsara/postgres/athena/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}
path "v1.1/cermati/indodana/db/rds/postgres/athena/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/indodana/kv/athena" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/indodana/kv/athena/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/auth/v1.1/cermati/indodana/athena/dev" {
  capabilities = ["read", "create", "update", "list", "sudo"]
}
path "auth/v1.1/cermati/indodana/athena/dev/role/*" {
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
# path "sys/mounts/v1.1.alt/db/hosted/postgres/cermati/indodana/athena/+/dev" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/athena/+/dev/config/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/athena/+/dev/roles/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/indodana/athena/+/dev/creds/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "sys/mounts/v1.1.alt/kv/cermati/indodana/athena" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/kv/cermati/indodana/athena/*" {
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
# path "v1.0/cermati/indodana/db/hosted/postgres/config/dev-athena-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.0/cermati/indodana/db/hosted/postgres/roles/dev-athena-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.0/cermati/indodana/db/hosted/postgres/creds/dev-athena-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "sys/mounts/v1.0/cermati/indodana/kv/athena" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "v1.0/cermati/indodana/kv/athena/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------
