node {
    // uncomment these 2 lines and edit the name 'node-4.4.5' according to what you choose in configuration
    def nodeHome = tool name: 'NodeJS 7.2.1', type: 'jenkins.plugins.nodejs.tools.NodeJSInstallation'
    env.PATH = "${nodeHome}/bin:${env.PATH}"

    stage("front") {
        dir('front') { // switch to subdir
            git url: ...             
            sh "npm install"

            sh "npm run build --prod"

            sh "cp -R * ../dist"
        }
    }

    stage("back") {
        dir('back') {
            git url: ...

            sh 'curl -sS https://getcomposer.org/installer | php'
            sh 'php composer.phar install'

            sh "cp -R * ../dist"
        }
    }
    stage("upload via ftp") {
        // IM NOT SURE WHAT TO DO HERE
    }
}
