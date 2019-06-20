
# ---------------------------------------------------------------------
# v1.1.0 - supported +
# ---------------------------------------------------------------------
path "sys/mounts/v1.1/cermati/team/db/hosted/postgres/product/+/prod" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/hosted/postgres/product/+/prod/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/hosted/postgres/product/+/prod/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/hosted/postgres/product/+/prod/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/team/db/rds/postgres/product/+/prod" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/rds/postgres/product/+/prod/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/rds/postgres/product/+/prod/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/rds/postgres/product/+/prod/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/team/db/apsara/postgres/product/+/prod" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/apsara/postgres/product/+/prod/config/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/apsara/postgres/product/+/prod/roles/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/db/apsara/postgres/product/+/prod/creds/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/mounts/v1.1/cermati/team/kv/product" {
  capabilities = ["read", "create", "update", "list"]
}

path "v1.1/cermati/team/kv/product/*" {
  capabilities = ["read", "create", "update", "list"]
}

path "sys/auth/v1.1/cermati/team/product/prod" {
  capabilities = ["read", "create", "update", "list", "sudo"]
}
path "auth/v1.1/cermati/team/product/prod/role/*" {
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
# path "sys/mounts/v1.1.alt/db/hosted/postgres/cermati/team/product/+/prod" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/team/product/+/prod/config/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/team/product/+/prod/roles/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.1.alt/db/hosted/postgres/cermati/team/product/+/prod/creds/*" {
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
# path "v1.0/cermati/team/db/hosted/postgres/config/prod-product-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.0/cermati/team/db/hosted/postgres/roles/prod-product-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# 
# path "v1.0/cermati/team/db/hosted/postgres/creds/prod-product-*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "sys/mounts/v1.0/cermati/team/kv/product" {
#   capabilities = ["read", "create", "update", "list"]
# }
# path "v1.0/cermati/team/kv/product/*" {
#   capabilities = ["read", "create", "update", "list"]
# }
# # ---------------------------------------------------------------------
