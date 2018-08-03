Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'NovaBackupTool',
            path: '/NovaBackupTool',
            component: require('./components/Tool'),
        },
    ])
})
