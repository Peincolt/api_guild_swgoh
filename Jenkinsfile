pipeline {
    agent any

    stages {
        stage('Copy Secret File') {
            steps {
                // Utiliser withCredentials pour récupérer le fichier secret et le copier
                withCredentials([file(credentialsId: 'secret_api_swgoh', variable: 'SECRET_ENV_FILE')]) {
                    // Le fichier secret est copié et disponible sous la variable $SECRET_ENV_FILE
                    sh 'cp -f $SECRET_ENV_FILE ./api/.env'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                script {
                    sh 'cd ./api && composer install --no-interaction'
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh 'docker-compose -f docker-compose.yaml up --build -d'
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    sh 'docker-compose -f docker-compose.yaml down && docker-compose -f docker-compose.yaml up -d'
                }
            }
        }
    }
}