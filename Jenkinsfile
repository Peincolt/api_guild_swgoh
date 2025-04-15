pipeline {
    agent any

    stages {
        stage('Copy Secret File') {
            steps {
                // Utiliser withCredentials pour récupérer le fichier secret et le copier
                withCredentials([file(credentialsId: 'secret_api_swgoh', variable: 'SECRET_ENV_FILE')]) {
                    // Le fichier secret est copié et disponible sous la variable $SECRET_ENV_FILE
                    sh 'cp $SECRET_ENV_FILE ./api/.env'
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

        stage('Run Tests') {
            steps {
                script {
                    sh 'cd ./api && vendor/bin/phpunit'
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh 'docker-compose -f docker-compose.prod.yml up --build -d'
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    sh 'docker-compose -f docker-compose.prod.yml down && docker-compose -f docker-compose.prod.yml up -d'
                }
            }
        }
    }
}