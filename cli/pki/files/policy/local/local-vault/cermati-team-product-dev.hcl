
# ---------------------------------------------------------------------
# v1.1.0 - supported +
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/team/db/hosted/postgres/product/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/hosted/postgres/product/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/hosted/postgres/product/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/hosted/postgres/product/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/team/db/rds/postgres/product/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/rds/postgres/product/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/rds/postgres/product/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/rds/postgres/product/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/team/db/apsara/postgres/product/+/dev" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/apsara/postgres/product/+/dev/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/apsara/postgres/product/+/dev/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}
path "v1.1/cermati/team/db/rds/postgres/product/+/dev/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/team/kv/product" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/kv/product/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/auth/v1.1/cermati/team/product/dev" {
  capabilities = ["read", "create", "update", "list", "sudo"]
}
path "auth/v1.1/cermati/team/product/dev/role/*" {
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
# path "sys/mounts/v1.1.alt/db/hosted/postgres/cermati/team/product/+/dev" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/team/product/+/dev/config/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/team/product/+/dev/roles/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/team/product/+/dev/creds/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "sys/mounts/v1.1.alt/kv/cermati/team/product" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/kv/cermati/team/product/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------
 

# # ---------------------------------------------------------------------
# # before v1.1.0 - not supported + yet, the structure is a bit different
# # ---------------------------------------------------------------------
# path "sys/mounts/v1.0/cermati/team/db/hosted/postgres" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.0/cermati/team/db/hosted/postgres/config/dev-product-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.0/cermati/team/db/hosted/postgres/roles/dev-product-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.0/cermati/team/db/hosted/postgres/creds/dev-product-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "sys/mounts/v1.0/cermati/team/kv/product" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "v1.0/cermati/team/kv/product/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------
