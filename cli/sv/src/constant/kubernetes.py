# Mapping between simplified format and formal Kubernetes format
TEMPLATE_MAP = {
  'DEPLOYMENT': [
    {
      'source': ['deployment', 'labels'],
      'destination': ['metadata', 'labels']
    },
    {
      'source': ['pod', 'replicas'],
      'destination': ['spec', 'replicas']
    },
    {
      'source': ['pod', 'labels'],
      'destination': ['spec', 'template', 'metadata', 'labels']
    },
    {
      'source': ['pod', 'requests'],
      'destination': ['spec', 'template', 'spec', 'containers', 0, 'resources', 'requests']
    },
    {
      'source': ['pod', 'ports'],
      'destination': ['spec', 'template', 'spec', 'containers', 0, 'ports']
    },
    {
      'source': ['pod', 'mainContainer'],
      'destination': ['spec', 'template', 'spec', 'containers', 0]
    },
    {
      'source': ['pod', 'sidecarContainers'],
      'destination': ['spec', 'template', 'spec', 'containers']
    },
    {
      'source': ['pod', 'logging', 'parser'],
      'destination': ['spec', 'template', 'metadata', 'labels', 'cermati.k8s.logging/parser']
    },
    {
      'source': ['pod', 'logging', 'exclude'],
      'destination': ['spec', 'template', 'metadata', 'labels', 'cermati.k8s.logging/exclude']
    },
    {
      'source': ['pod', 'logging', 'namespace'],
      'destination': ['spec', 'template', 'metadata', 'labels', 'cermati.k8s.logging/namespace']
    }
  ],

  'SERVICE': [
    {
      'source': ['labels'],
      'destination': ['metadata', 'labels']
    },
    {
      'source': ['type'],
      'destination': ['spec', 'type']
    },
    {
      'source': ['ports'],
      'destination': ['spec', 'ports']
    }
  ]
}

# Additional vendor-specific configuration for LoadBalancer services
CLOUD_PROVIDER_LOADBALANCER_MAP = {
  'aliyun': {
    'type': {
      'internal': {
        'annotations': {
          'service.beta.kubernetes.io/alicloud-loadbalancer-address-type': 'intranet'
        }
      },
     'external': {
        'annotations': {
          'service.beta.kubernetes.io/alicloud-loadbalancer-address-type': 'internet'
        }
      }
    }
  }
}
