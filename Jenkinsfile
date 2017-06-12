pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                sh 'zip -9r site.zip include public_html'
                archiveArtifacts artifacts: 'site.zip', fingerprint: true
            }
        }
    }
}
