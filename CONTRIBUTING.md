# Contribuer au projet

Ce document détaille les règles à suivre pour contribuer  au projet.


## Conventions de nommage des branches 

### Branches

Utilisez le format suivant pour nommer vos branches :

- `feature/description` : pour une nouvelle fonctionnalité.

- `fix/description` : pour une correction de bug.

- `doc/description` : pour une mise à jour de la documentation.

Exemples :
```text
feature/ajout-authentification
fix/correction-bug-paiement
```

## Procédure pour contribuer au projet

**1) Forkez le dépôt principal** sur GitHub.

**2) Clonez votre fork** en local :
   ```bash
   git clone https://github.com/<votre-nom-utilisateur>/<nom-du-repo>.git
   cd <nom-du-repo>
   ```

**3) Créez une branche** selon les conventions décrites ci-dessus :
   ```bash
   git checkout -b feature/nouvelle-fonctionnalite
   ```

**4) Faites vos modifications** et vérifiez qu'elles respectent les bonnes pratiques.

**5) Commitez vos changements** avec un message descriptif.

**6) Poussez vos modifications** sur votre fork :
   ```bash
   git push origin feature/nouvelle-fonctionnalite
   ```

**7) Créez une Pull Request** vers le dépôt principal :
   - Utilisez un titre descriptif.
   
   - Ajoutez une description détaillée de vos modifications, le problème qu'elles résolvent et les tests effectués.



## Validation des contributions

**Pour qu'une contribution soit acceptée :**

- Tous les tests doivent passer sans erreur.

- Le code doit respecter les standards PSR-12.


## Bonnes pratiques

- **Tests** : ajoutez des tests unitaires et fonctionnels pour couvrir vos modifications.

- **Analyse du code** : utilisez des outils comme `phpstan`, `eslint`, ou `prettier` selon les langages utilisés.

- **Documentation** : ajoutez une documentation pour expliquer vos modifications.

## Soumettre des problèmes

- **Recherchez dans les issues existantes** : vérifiez si votre problème n'a pas déjà été signalé.

- **Créez une nouvelle issue** : fournissez un titre clair et décrivez le problème en détail (étapes pour reproduire le problème, comportement attendu et observé).
     

## Proposer des fonctionnalités

- **Vérifiez les demandes existantes** : assurez-vous que votre idée n'a pas déjà été proposée.

- **Créez une nouvelle issue** pour suggérer une fonctionnalité : décrivez le problème que cette fonctionnalité résout et fournissez une description détaillée de la solution que vous proposez.